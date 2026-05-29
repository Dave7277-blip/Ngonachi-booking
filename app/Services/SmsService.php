<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiKey;
    private string $username;
    private string $senderId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey   = env('AFRICASTALKING_API_KEY', '');
        $this->username = env('AFRICASTALKING_USERNAME', 'sandbox');
        $this->senderId = env('AFRICASTALKING_SENDER_ID', 'NgonStudio');

        // Use sandbox URL for testing, live URL for production
        $this->baseUrl = $this->username === 'sandbox'
            ? 'https://api.sandbox.africastalking.com/version1/messaging'
            : 'https://api.africastalking.com/version1/messaging';
    }

    /**
     * Send an SMS message to a phone number.
     *
     * @param  string  $phone    Recipient phone number e.g. +255712345678
     * @param  string  $message  SMS message text (max 160 chars for single SMS)
     * @return bool
     */
    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey) || empty($this->username)) {
            Log::warning('SMS not sent — AFRICASTALKING credentials not configured.');
            return false;
        }

        // Ensure phone number is in international format
        $phone = $this->formatPhone($phone);

        try {
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->asForm()->post($this->baseUrl, [
                'username' => $this->username,
                'to'       => $phone,
                'message'  => $message,
                'from'     => $this->senderId,
            ]);

            $body = $response->json();

            // Check if at least one recipient was queued successfully
            $recipients = $body['SMSMessageData']['Recipients'] ?? [];
            foreach ($recipients as $recipient) {
                if (in_array($recipient['status'], ['Success', 'MessageSent'])) {
                    Log::info('SMS sent successfully', ['phone' => $phone]);
                    return true;
                }
            }

            Log::warning('SMS sending failed', [
                'phone'    => $phone,
                'response' => $body,
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('SMS exception', [
                'phone'     => $phone,
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Format phone number to international format.
     * Converts 0712345678 → +255712345678
     */
    private function formatPhone(string $phone): string
    {
        // Remove all spaces, dashes, brackets
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Already in international format
        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Tanzania local format: 07XXXXXXXX or 06XXXXXXXX
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '+255' . substr($phone, 1);
        }

        // Already has country code without +
        if (str_starts_with($phone, '255') && strlen($phone) === 12) {
            return '+' . $phone;
        }

        return $phone;
    }
}