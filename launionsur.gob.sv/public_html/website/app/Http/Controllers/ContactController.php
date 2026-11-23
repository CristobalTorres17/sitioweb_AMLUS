<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact.index', [
            'title' => 'Contacto',
        ]);
    }

    public function send(ContactRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        Mail::to(config('mail.contact_recipient'))
            ->send(new ContactMail($payload));

        return back()->with('success', 'Mensaje enviado. Nos pondremos en contacto pronto.');
    }
}
