<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\GeneralSetting;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $code;
    public $isWelcome;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code, bool $isWelcome = false)
    {
        $this->code = $code;
        $this->isWelcome = $isWelcome;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $settings = null;
        try {
            $settings = GeneralSetting::first();
        } catch (\Throwable $e) {}

        $schoolName = $settings->school_name ?? $settings->site_title ?? 'YASEEN ENGLISH SCHOOL - MALOORA';

        // Read Frontend URL strictly from .env (FRONTEND_URL)
        $frontendUrl = config('app.frontend_url') ?: env('FRONTEND_URL') ?: 'http://localhost:3000';
        $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $this->code . '&email=' . urlencode($notifiable->email);

        $recipientName = $notifiable->full_name ?? $notifiable->name ?? 'User';

        $defaultLogo = 'https://lh3.googleusercontent.com/aida-public/AB6AXuAjM3C-1JQIXVdMEscPESSpPjbNOCbADVO8r_kz5zv6SO-lH3CcF6AwY_cYHnlwlJWRdxjNMVGD49xwBoZdsL9nicNlThm414efTX5wIwveLll_mQxwpmwcnAN8_1khhD8PuHQuqBHqw8T2joUfiBK0U2KgWXd-YHBuriC4YLWvaowpV1ByG-cdZ-duhY3X-ZyqqxIfXVUs7axVbORbVwXx4GsGg2vIH6i0A7BPOebGaI1aah54gQ4';
        $logoUrl = ($settings && !empty($settings->logo)) ? asset($settings->logo) : $defaultLogo;

        // Read Backend URL strictly from .env (APP_URL)
        $backendUrl = config('app.url') ?: env('APP_URL') ?: url('/');
        $headerImageUrl = rtrim($backendUrl, '/') . '/images/email-header.jpg';

        $subject = $this->isWelcome
            ? ($schoolName . ' - Welcome! Set Up Your Account Password')
            : ($schoolName . ' - Password Reset Verification Code');

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.reset_password', [
                'schoolName'     => $schoolName,
                'recipientName'  => $recipientName,
                'code'           => $this->code,
                'resetUrl'       => $resetUrl,
                'expireMinutes'  => config('auth.passwords.users.expire', 60),
                'logoUrl'        => $logoUrl,
                'headerImageUrl' => $headerImageUrl,
                'isWelcome'      => $this->isWelcome,
            ]);
    }
}
