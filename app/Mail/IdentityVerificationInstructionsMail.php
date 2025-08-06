<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IdentityVerificationInstructionsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verificationUrl = null)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl ?? route('user.kyc.index');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Identity Verification Instructions - ' . config('app.name'),
            from: config('mail.from.address', 'info@payperviews.net'),
            replyTo: config('mail.from.address', 'info@payperviews.net'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.identity-verification-instructions',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'siteName' => config('app.name'),
                'supportEmail' => config('mail.from.address'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
