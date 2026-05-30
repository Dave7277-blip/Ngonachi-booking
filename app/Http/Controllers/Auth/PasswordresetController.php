<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * POST /api/auth/forgot-password
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', 'admin')
                    ->first();

        // Always return success to prevent email enumeration
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If that email exists in our system, a reset link has been sent.',
            ]);
        }

        // Delete any old tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Generate token — only alphanumeric, no special characters
        // so it passes through URLs without encoding issues
        $token = Str::upper(Str::random(6));  // e.g. A3BX9K — simple 6 char code

        // Store hashed token
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $frontendUrl = env('FRONTEND_URL', 'http://127.0.0.1:5500');
        $resetUrl    = $frontendUrl . '/index.html?reset_token=' . $token
                       . '&email=' . urlencode($request->email);

        try {
            Mail::send([], [], function ($mail) use ($user, $resetUrl, $token) {
                $mail->to($user->email)
                     ->subject('Password Reset — Ngonachi Studios Admin')
                     ->html("
                        <div style='font-family:Georgia,serif;max-width:500px;margin:40px auto;
                                    background:#fff;border:1px solid #E8D5B0;border-radius:8px;
                                    overflow:hidden'>
                            <div style='background:#1A1612;padding:28px;text-align:center'>
                                <p style='color:#C9A96E;font-size:22px;letter-spacing:4px;margin:0'>
                                    NGONACHI STUDIOS
                                </p>
                            </div>
                            <div style='padding:32px'>
                                <h2 style='color:#1A1612;font-weight:400;margin-top:0'>
                                    Password Reset Request
                                </h2>
                                <p style='color:#4A4035;line-height:1.8;font-size:14px'>
                                    Hello {$user->name},<br><br>
                                    You requested a password reset.
                                    Click the button below or use the code below.
                                    This link expires in <strong>60 minutes</strong>.
                                </p>
                                <div style='text-align:center;margin:24px 0'>
                                    <a href='{$resetUrl}'
                                       style='background:#C9A96E;color:#1A1612;padding:14px 32px;
                                              text-decoration:none;border-radius:4px;font-size:14px;
                                              font-family:Arial,sans-serif;font-weight:bold;
                                              display:inline-block'>
                                        Reset My Password
                                    </a>
                                </div>
                                <div style='background:#FAF7F2;border:2px dashed #C9A96E;
                                            border-radius:8px;padding:20px;text-align:center;
                                            margin:20px 0'>
                                    <p style='color:#8C7B6B;font-size:12px;margin:0 0 8px'>
                                        Or enter this code manually on the reset page:
                                    </p>
                                    <p style='font-family:monospace;font-size:32px;
                                              font-weight:bold;color:#1A1612;
                                              letter-spacing:8px;margin:0'>
                                        {$token}
                                    </p>
                                </div>
                                <p style='color:#8C7B6B;font-size:12px;line-height:1.7'>
                                    If you did not request this, ignore this email.
                                    Your password will not change.
                                </p>
                            </div>
                            <div style='background:#1A1612;padding:18px;text-align:center;
                                        color:rgba(255,255,255,0.4);font-size:12px'>
                                &copy; " . date('Y') . " Ngonachi Studios
                            </div>
                        </div>
                     ");
            });
        } catch (\Throwable $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset email. Please check your mail configuration.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reset link sent! Check your email inbox. The link expires in 60 minutes.',
        ]);
    }

    /**
     * POST /api/auth/reset-password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'No reset request found for this email. Please request a new link.',
            ], 422);
        }

        // Check expiry — 60 minutes
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset link has expired. Please request a new one.',
            ], 422);
        }

        // Verify token — check both uppercase and as-is
        $tokenMatches = Hash::check($request->token, $record->token)
                     || Hash::check(strtoupper($request->token), $record->token);

        if (!$tokenMatches) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset code. Please check and try again.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        // Update password
        $user->update(['password' => Hash::make($request->password)]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Revoke all API tokens — forces re-login
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. You can now log in with your new password.',
        ]);
    }
}