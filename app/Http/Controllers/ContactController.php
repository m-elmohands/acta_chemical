<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /** @unauthenticated */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'msg' => 'required|string',
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 422);
        }

        $validated = $validation->validated();
        $contact = Contact::create($validated);

        $toEmail = Setting::first()?->email;

        if ($toEmail) {
            Mail::raw(
                "New contact message:\n\n".
                "Name: {$contact->name}\n".
                "Email: {$contact->email}\n".
                "Phone: {$contact->phone}\n".
                "Subject: {$contact->subject}\n\n".
                "Message:\n{$contact->msg}",
                function ($message) use ($contact, $toEmail) {
                    $message->to($toEmail)
                        ->subject("New Message: {$contact->subject}");
                }
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully.',
            'data' => $contact,
        ], 201);
    }
}
