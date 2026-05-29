<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * POST /api/contacts
     * Save a contact form submission (public, no auth required).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'min:2', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received. We will respond within 24 hours.',
        ], 201);
    }

    /**
     * GET /api/admin/contacts
     * List all contact messages with optional unread filter (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contact::latest();

        if ($request->boolean('unread')) {
            $query->unread();
        }

        return response()->json([
            'success'  => true,
            'messages' => $query->paginate((int) $request->input('per_page', 20)),
            'unread_count' => Contact::unread()->count(),
        ]);
    }

    /**
     * PATCH /api/admin/contacts/{contact}/read
     * Mark a message as read (admin).
     */
    public function markRead(Contact $contact): JsonResponse
    {
        if (! $contact->is_read) {
            $contact->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read.',
            'contact' => $contact->fresh(),
        ]);
    }

    /**
     * DELETE /api/admin/contacts/{contact}
     * Delete a contact message (admin).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted.',
        ]);
    }
}