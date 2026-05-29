<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        //
    }

    public function envelope(): Envelope
    {
        $statusLabel = ucfirst($this->booking->status);

        return new Envelope(
            subject: "Booking {$this->booking->reference} — {$statusLabel} | Lumière Studios",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-status-update',
        );
    }
}