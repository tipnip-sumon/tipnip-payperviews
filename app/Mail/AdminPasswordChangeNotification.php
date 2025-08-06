<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPasswordChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $ipAddress;
    public $userAgent;
    public $changeTime; 

    /**
     * Create a new message instance.
     */
    public function __construct($admin, $ipAddress, $userAgent, $changeTime)
    {
        $this->admin = $admin;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->changeTime = $changeTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admin Password Changed - Security Alert',
            from: config('mail.from.address', 'security@payperviews.net'),
            replyTo: config('mail.from.address', 'security@payperviews.net'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-password-change',
            text: 'emails.admin-password-change-text',
            with: [
                'admin' => $this->admin,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'changeTime' => $this->changeTime,
                'appName' => config('app.name', 'PayPerViews'),
                'supportEmail' => config('mail.from.address', 'support@payperviews.net'),
            ],
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
