<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $contact = Contact::create([
            'name' => strip_tags($validated['name']),
            'email' => strip_tags($validated['email']),
            'subject' => isset($validated['subject']) ? strip_tags($validated['subject']) : null,
            'message' => strip_tags($validated['message']),
        ]);

        return response()->json([
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'data' => $contact
        ], 201);
    }
}
