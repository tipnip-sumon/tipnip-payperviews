<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Invest;
use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FirstInvestmentCongratulationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $investment;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Invest $investment)
    {
        $this->user = $user;
        $this->investment = $investment;
        $this->settings = GeneralSetting::getSettings();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Congratulations on Your First Investment!',
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
            view: 'emails.first-investment-congratulation',
            with: [
                'user' => $this->user,
                'investment' => $this->investment,
                'settings' => $this->settings,
                'dashboardUrl' => route('user.dashboard'),
                'investmentUrl' => route('user.invest.log'),
                'profileUrl' => route('user.profile.setting'),
                'supportEmail' => $this->settings->email_from ?? config('mail.from.address'),
                'investmentDate' => $this->investment->created_at->format('F j, Y \a\t g:i A'),
                'maturityDate' => $this->investment->next_time ? $this->investment->next_time->format('F j, Y') : 'Ongoing',
                'planName' => $this->investment->plan->name ?? 'Investment Plan',
                'planDetails' => [
                    'return_type' => $this->investment->plan->return_type ?? 'percentage',
                    'interest' => $this->investment->plan->interest ?? 0,
                    'time' => $this->investment->plan->time ?? 0,
                    'time_name' => $this->investment->plan->time_name ?? 'days'
                ]
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
