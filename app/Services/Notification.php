<?php

namespace App\Services;

use App\Services\Notification\Email;
use App\Services\Notification\Telegram;

class Notification
{
    protected $emailService;
    protected $telegramService;
    protected $channels;

    public function __construct()
    {
        $this->emailService = app(Email::class);
        $this->telegramService = app(Telegram::class);
        $this->channels = include app_path('Services/config.php');
    }

    public function send(array $destinations, array $data)
    {
        // Separate destinations by channel type
        $emailDestinations = [];
        $telegramDestinations = [];

        foreach ($destinations as $destination) {
            if (stripos($destination, '@') !== false) {
                $emailDestinations[] = $destination;
            } else {
                $telegramDestinations[] = $destination;
            }
        }

        // Send verification via email
        if (!empty($emailDestinations) && in_array('email', $this->channels['otp'])) {
            foreach ($emailDestinations as $email) {
                $this->sendVerificationEmail($data['appName'], $email, $data['verification']);
            }
        }

        // Send verification via Telegram
        if (!empty($telegramDestinations) && in_array('telegram', $this->channels['otp'])) {
            foreach ($telegramDestinations as $telegramId) {
                $this->sendVerificationTelegram($data['appName'], $telegramId, $data['verification']);
            }
        }
    }

    protected function sendVerificationEmail(string $appName, string $destination, string $verification)
    {
        $subject = "{$appName} Verification";
        $body = "Dear user, your verification number is: {$verification}. Please do not share this number with anyone. Thank you for using {$appName}.";
        $data = [
            'subject' => $subject,
            'body' => $body,
            'from_address' => 'no-reply@example.com', // Customize this address
            'from_name' => $appName
        ];

        $this->emailService->sendEmails([$destination], $data);
    }

    protected function sendVerificationTelegram(string $appName, string $destination, string $verification)
    {
        $text = "Your verification number is: {$verification}. Please do not share this number with anyone. Thank you for using {$appName}.";
        $this->telegramService->sendMessages([$destination], $text);
    }
}
