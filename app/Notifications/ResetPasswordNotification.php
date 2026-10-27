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

        $schoolName = $settings->school_name ?? $settings->site_title ?? config('app.name', 'ERP School');
        $frontendUrl = env('FRONTEND_URL', config('app.url'));
        $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $this->code . '&email=' . urlencode($notifiable->email);

        $recipientName = $notifiable->full_name ?? $notifiable->name ?? 'User';

        if ($this->isWelcome) {
            return (new MailMessage)
                ->subject($schoolName . ' - Welcome! Set Up Your Account Password')
                ->greeting('Dear ' . $recipientName . ',')
                ->line('Welcome to the **' . $schoolName . ' Portal**! Your account has been successfully created.')
                ->line('To activate your account and set up your personal login password, please use the following 6-digit verification code or click the button below:')
                ->line('# **' . $this->code . '**')
                ->action('Set Up Password', $resetUrl)
                ->line('⏱️ This verification code is valid for **' . config('auth.passwords.users.expire', 60) . ' minutes**.')
                ->line('🔒 **Security Notice:** For your security, do not share this code with anyone.')
                ->salutation('Warm regards,' . "\n" . '**' . $schoolName . ' Portal Support**');
        }

        return (new MailMessage)
            ->subject($schoolName . ' - Password Reset Verification Code')
            ->greeting('Dear ' . $recipientName . ',')
            ->line('We received a request to reset your password for your **' . $schoolName . ' Portal** account.')
            ->line('Please use the following 6-digit One-Time Password (OTP) to verify your request:')
            ->line('# **' . $this->code . '**')
            ->action('Reset Password', $resetUrl)
            ->line('⏱️ This verification code is valid for **' . config('auth.passwords.users.expire', 60) . ' minutes**.')
            ->line('🔒 **Security Notice:** For your security, do not share this OTP with anyone. School administration will never ask for your code.')
            ->line('If you did not request a password reset, please ignore this email or notify the school IT helpdesk immediately.')
            ->salutation('Warm regards,' . "\n" . '**' . $schoolName . ' Portal Support**');
    }
}
