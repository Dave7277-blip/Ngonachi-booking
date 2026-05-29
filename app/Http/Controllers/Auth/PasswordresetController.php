<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * POST /api/auth/forgot-password
     * Generate a reset token and email it to the admin.
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
                'message' => 'If that email exists, a reset link has been sent.',
            ]);
        }

        // Delete any existing reset tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Generate a secure token
        $token = Str::random(64);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Build the reset URL pointing to your frontend
        $frontendUrl = env('FRONTEND_URL', 'http://127.0.0.1:5500');
        $resetUrl    = $frontendUrl . '/index.html?reset_token=' . $token . '&email=' . urlencode($request->email);

        // Send reset email
        try {
            Mail::send([], [], function ($mail) use ($user, $resetUrl) {
                $mail->to($user->email)
                     ->subject('Password Reset — Ngonachi Studios Admin')
                     ->html("
                        <div style='font-family:Georgia,serif;max-width:500px;margin:40px auto;background:#fff;border:1px solid #E8D5B0;border-radius:8px;overflow:hidden'>
                            <div style='background:#1A1612;padding:28px;text-align:center'>
                                <p style='color:#C9A96E;font-size:22px;letter-spacing:4px;margin:0'>NGONACHI STUDIOS</p>
                            </div>
                            <div style='padding:32px'>
                                <h2 style='color:#1A1612;font-weight:400;margin-top:0'>Password Reset Request</h2>
                                <p style='color:#4A4035;line-height:1.8;font-size:14px'>
                                    Hello {$user->name},<br><br>
                                    You requested a password reset for your admin account.
                                    Click the button below to set a new password.
                                    This link expires in <strong>60 minutes</strong>.
                                </p>
                                <div style='text-align:center;margin:28px 0'>
                                    <a href='{$resetUrl}'
                                       style='background:#C9A96E;color:#1A1612;padding:14px 32px;
                                              text-decoration:none;border-radius:4px;font-size:14px;
                                              font-family:Arial,sans-serif;font-weight:bold;
                                              letter-spacing:1px;display:inline-block'>
                                        Reset My Password
                                    </a>
                                </div>
                                <p style='color:#8C7B6B;font-size:13px;line-height:1.7'>
                                    If you did not request this, you can safely ignore this email.
                                    Your password will not change.<br><br>
                                    Or copy this link into your browser:<br>
                                    <a href='{$resetUrl}' style='color:#C9A96E;word-break:break-all'>{$resetUrl}</a>
                                </p>
                            </div>
                            <div style='background:#1A1612;padding:18px;text-align:center;color:rgba(255,255,255,0.4);font-size:12px;font-family:Arial,sans-serif'>
                                &copy; " . date('Y') . " Ngonachi Studios
                            </div>
                        </div>
                     ");
            });
        } catch (\Throwable $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Reset link sent! Check your email inbox.',
        ]);
    }

    /**
     * POST /api/auth/reset-password
     * Validate the token and update the password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        // Find the reset record
        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset link. Please request a new one.',
            ], 422);
        }

        // Check token is not expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset link has expired. Please request a new one.',
            ], 422);
        }

        // Verify token
        if (!Hash::check($request->token, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset link. Please request a new one.',
            ], 422);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Revoke all existing API tokens so old sessions are invalidated
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. You can now log in.',
        ]);
    }
}