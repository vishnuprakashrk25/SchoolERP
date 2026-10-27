<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AccountNotificationService
{
    /**
     * Send welcome & password reset setup email to a newly created user (Student, Staff, Teacher, Parent).
     *
     * @param User|string $userOrEmail User model or email address
     * @param string|null $name Optional recipient name if email is passed
     * @return bool
     */
    public static function sendWelcomeResetEmail($userOrEmail, ?string $name = null): bool
    {
        $user = null;
        $email = null;

        if ($userOrEmail instanceof User) {
            $user = $userOrEmail;
            $email = $user->email;
        } elseif (is_string($userOrEmail)) {
            $email = $userOrEmail;
            $user = User::where('email', $email)->first();
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!$user) {
            Log::warning("Cannot send welcome reset email: user not found for {$email}");
            return false;
        }

        try {
            // Generate 6-digit numeric OTP code
            $code = sprintf("%06d", mt_rand(100000, 999999));

            // Store hashed code in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token'      => Hash::make($code),
                    'created_at' => Carbon::now(),
                ]
            );

            // Send notification with welcome flag set to true
            $user->notify(new ResetPasswordNotification($code, true));

            return true;
        } catch (\Throwable $e) {
            // Ensure email delivery failures do not break the transaction or account creation
            Log::warning("Failed sending welcome password reset email to {$email}: " . $e->getMessage());
            return false;
        }
    }
}
