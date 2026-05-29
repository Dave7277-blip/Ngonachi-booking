<?php

namespace App\Http\Controllers;

use App\Mail\AdminBookingAlert;
use App\Mail\BookingConfirmation;
use App\Mail\BookingStatusUpdate;
use App\Models\Booking;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function __construct(private SmsService $sms) {}

    // ────────────────────────────────────────────────────────
    // PUBLIC — Submit a booking
    // ────────────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_name'    => ['required', 'string', 'min:2', 'max:255'],
            'client_email'   => ['required', 'email', 'max:255'],
            'client_phone'   => ['required', 'string', 'min:7', 'max:30'],
            'package_id'     => ['required', 'integer', 'exists:packages,id'],
            'event_type'     => ['required', 'in:wedding,sendoff'],
            'event_date'     => ['required', 'date', 'after:today'],
            'event_location' => ['required', 'string', 'min:3', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        // 1. Save to database
        $booking = DB::transaction(function () use ($validated) {
            return Booking::create(array_merge($validated, [
                'reference' => Booking::generateReference(),
                'status'    => 'pending',
            ]));
        });

        $booking->load('package');

        // 2. Email client — confirmation
        try {
            Mail::to($booking->client_email)
                ->send(new BookingConfirmation($booking));
        } catch (\Throwable $e) {
            Log::error('Client confirmation email failed', ['ref' => $booking->reference, 'error' => $e->getMessage()]);
        }

        // 3. SMS client — confirmation
        $this->sms->send(
            $booking->client_phone,
            "Hi {$booking->client_name}, your booking ({$booking->reference}) at Ngonachi Studios has been received! We will confirm within 24hrs. Date: {$booking->event_date->format('d M Y')}."
        );

        // 4. Email admin — new booking alert
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@lumiere.co.tz');
            Mail::to($adminEmail)->send(new AdminBookingAlert($booking));
        } catch (\Throwable $e) {
            Log::error('Admin alert email failed', ['ref' => $booking->reference, 'error' => $e->getMessage()]);
        }

        // 5. SMS admin — new booking alert
        $adminPhone = env('ADMIN_PHONE', '');
        if ($adminPhone) {
            $this->sms->send(
                $adminPhone,
                "NEW BOOKING [{$booking->reference}] from {$booking->client_name} ({$booking->client_phone}). Package: {$booking->package->name}. Date: {$booking->event_date->format('d M Y')}. Log in to review."
            );
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Booking submitted successfully. We will confirm within 24 hours.',
            'reference' => $booking->reference,
            'booking'   => $booking,
        ], 201);
    }

    // ────────────────────────────────────────────────────────
    // ADMIN — List bookings
    // ────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $query = Booking::with('package')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('client_name',   'like', "%{$term}%")
                  ->orWhere('client_email', 'like', "%{$term}%")
                  ->orWhere('reference',    'like', "%{$term}%");
            });
        }

        return response()->json([
            'success'  => true,
            'bookings' => $query->paginate((int) $request->input('per_page', 20)),
            'stats'    => $this->getStats(),
        ]);
    }

    // ────────────────────────────────────────────────────────
    // ADMIN — Show single booking
    // ────────────────────────────────────────────────────────

    public function show(Booking $booking): JsonResponse
    {
        return response()->json([
            'success' => true,
            'booking' => $booking->load('package'),
        ]);
    }

    // ────────────────────────────────────────────────────────
    // ADMIN — Update booking status (approve/reject/complete)
    // ────────────────────────────────────────────────────────

    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'status'      => ['required', 'in:confirmed,completed,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $updates = [
            'status'      => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $booking->admin_notes,
        ];

        if ($validated['status'] === 'confirmed' && is_null($booking->confirmed_at)) {
            $updates['confirmed_at'] = now();
        }
        if ($validated['status'] === 'completed' && is_null($booking->completed_at)) {
            $updates['completed_at'] = now();
        }

        $booking->update($updates);
        $booking->load('package');

        // ── Build SMS message based on status ──────────────
        $smsMessages = [
            'confirmed' => "Hi {$booking->client_name}, GREAT NEWS! Your booking ({$booking->reference}) at Ngonachi Studios has been CONFIRMED. Date: {$booking->event_date->format('d M Y')} at {$booking->event_location}. We look forward to capturing your special day!",

            'completed' => "Hi {$booking->client_name}, your event ({$booking->reference}) has been marked as COMPLETED by Ngonachi Studios. Your photos will be delivered as per your package. Thank you for choosing us!",

            'rejected'  => "Hi {$booking->client_name}, unfortunately your booking ({$booking->reference}) at Ngonachi Studios could not be accommodated. Please contact us at " . env('ADMIN_PHONE', '') . " to discuss alternative dates.",
        ];

        // 1. SMS client — status update
        $this->sms->send(
            $booking->client_phone,
            $smsMessages[$validated['status']]
        );

        // 2. Email client — status update
        try {
            Mail::to($booking->client_email)
                ->send(new BookingStatusUpdate($booking));
        } catch (\Throwable $e) {
            Log::error('Status update email failed', ['ref' => $booking->reference, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Booking {$booking->reference} updated to '{$booking->status}'. Client notified by email and SMS.",
            'booking' => $booking->fresh('package'),
        ]);
    }

    // ────────────────────────────────────────────────────────
    // ADMIN — Delete booking
    // ────────────────────────────────────────────────────────

    public function destroy(Booking $booking): JsonResponse
    {
        $reference = $booking->reference;
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => "Booking {$reference} has been deleted.",
        ]);
    }

    // ────────────────────────────────────────────────────────
    // ADMIN — Dashboard summary
    // ────────────────────────────────────────────────────────

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'stats'    => $this->getStats(),
            'recent'   => Booking::with('package')->latest()->take(5)->get(),
            'upcoming' => Booking::upcoming()->with('package')->take(8)->get(),
        ]);
    }

    // ────────────────────────────────────────────────────────
    // PRIVATE helper
    // ────────────────────────────────────────────────────────

    private function getStats(): array
    {
        return [
            'total'     => Booking::count(),
            'pending'   => Booking::pending()->count(),
            'confirmed' => Booking::confirmed()->count(),
            'completed' => Booking::completed()->count(),
            'rejected'  => Booking::rejected()->count(),
        ];
    }
}