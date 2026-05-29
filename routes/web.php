<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json(['message' => 'Ngonachi Studios API is running.']);
});

/*
|--------------------------------------------------------------------------
| EMAIL TEST ROUTE
|--------------------------------------------------------------------------
| Visit: http://localhost:8000/test-email
| This sends a real test email so you can confirm Gmail SMTP is working.
| REMOVE THIS ROUTE before deploying to production.
|--------------------------------------------------------------------------
*/
Route::get('/test-email', function () {

    $to = request()->query('to', env('ADMIN_EMAIL', 'janjarodavid@gmail.com'));

    try {
        Mail::send([], [], function ($mail) use ($to) {
            $mail->to($to)
                 ->subject('✅ Ngonachi Studios — Email Test')
                 ->html("
                    <div style='font-family:Georgia,serif;max-width:500px;margin:40px auto;
                                background:#fff;border:2px solid #C9A96E;border-radius:8px;
                                overflow:hidden'>
                        <div style='background:#1A1612;padding:24px;text-align:center'>
                            <p style='color:#C9A96E;font-size:20px;letter-spacing:4px;margin:0'>
                                NGONACHI STUDIOS
                            </p>
                        </div>
                        <div style='padding:28px;text-align:center'>
                            <h2 style='color:#1A1612;font-weight:400'>
                                ✅ Email is Working!
                            </h2>
                            <p style='color:#4A4035;line-height:1.8;font-size:14px'>
                                Your Gmail SMTP is configured correctly.<br>
                                Booking notifications will now be delivered.
                            </p>
                            <p style='color:#8C7B6B;font-size:12px;margin-top:20px'>
                                Sent at: " . now()->format('d M Y, H:i:s') . "
                            </p>
                        </div>
                        <div style='background:#1A1612;padding:16px;text-align:center;
                                    color:rgba(255,255,255,0.4);font-size:12px'>
                            &copy; " . date('Y') . " Ngonachi Studios
                        </div>
                    </div>
                 ");
        });

        return response()->json([
            'success' => true,
            'message' => "Test email sent successfully to {$to}. Check your inbox.",
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Email failed: ' . $e->getMessage(),
            'fix'     => [
                'check_1' => 'Make sure MAIL_USERNAME and MAIL_PASSWORD are set in .env',
                'check_2' => 'MAIL_PASSWORD must be a Gmail App Password (16 chars, no spaces)',
                'check_3' => 'Gmail 2-Step Verification must be turned ON',
                'check_4' => 'Run: php artisan config:clear after editing .env',
            ],
        ], 500);
    }
});

/*
|--------------------------------------------------------------------------
| SMS TEST ROUTE
|--------------------------------------------------------------------------
| Visit: http://localhost:8000/test-sms?phone=+255712345678
| REMOVE THIS ROUTE before deploying to production.
|--------------------------------------------------------------------------
*/
Route::get('/test-sms', function () {

    $phone = request()->query('phone', env('ADMIN_PHONE', ''));

    if (empty($phone)) {
        return response()->json([
            'success' => false,
            'message' => 'No phone number. Use: /test-sms?phone=+255712345678',
        ]);
    }

    try {
        $sms = app(\App\Services\SmsService::class);
        $sent = $sms->send($phone, 'Test SMS from Ngonachi Studios. Your SMS notifications are working!');

        return response()->json([
            'success' => $sent,
            'message' => $sent
                ? "SMS sent to {$phone}. Check your phone."
                : "SMS failed. Check AFRICASTALKING credentials in .env",
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'SMS error: ' . $e->getMessage(),
        ], 500);
    }
});