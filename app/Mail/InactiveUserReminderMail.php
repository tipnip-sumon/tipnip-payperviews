<?php

namespace App\Mail;

use App\Models\User;
use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactiveUserReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->settings = GeneralSetting::getSettings();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '💰 Ready to Start Investing? Your Account is Waiting!',
            from: $this->settings->email_from ?? config('mail.from.address'),
            replyTo: $this->settings->email_from ?? config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.inactive-user-reminder',
            with: [
                'user' => $this->user,
                'settings' => $this->settings,
                'investUrl' => route('user.invest.plan'),
                'loginUrl' => route('user.login'),
                'dashboardUrl' => route('user.dashboard'),
                'supportEmail' => $this->settings->email_from ?? config('mail.from.address'),
                'daysSinceLastLogin' => $this->user->last_login_at 
                    ? $this->user->last_login_at->diffInDays(now()) 
                    : 'many'
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
