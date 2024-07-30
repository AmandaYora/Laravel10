<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;

class Telegram
{
    protected $telegramUrl;
    protected $telegramToken;

    public function __construct()
    {
        $this->telegramUrl = config('services.telegram.url');
        $this->telegramToken = config('services.telegram.token');
    }

    public function sendMessages(array $destinations, string $text)
    {
        $count = 0;
        $status = null;

        foreach ($destinations as $destination) {
            $response = $this->sendMessage($destination, $text);

            if ($response->successful()) {
                $status = 'sent';
            } else {
                $status = 'unsent';
            }

            $count++;
        }

        if ($count > 0) {
            $errorInfo = $count . ' Telegram messages sent';
            // Log or handle the information as needed
        } else {
            $errorInfo = 'Exception';
            // Handle the error case
        }

        return $errorInfo;
    }

    protected function sendMessage($destination, $text)
    {
        return Http::get($this->telegramUrl . $this->telegramToken . '/sendMessage', [
            'chat_id' => $destination,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}
