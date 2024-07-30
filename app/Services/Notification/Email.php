<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Mail;

class Email
{
    protected $smtpHost;
    protected $smtpPort;
    protected $smtpUsername;
    protected $smtpPassword;
    protected $smtpEncryption;

    public function __construct()
    {
        $this->smtpHost = config('services.smtp.host');
        $this->smtpPort = config('services.smtp.port');
        $this->smtpUsername = config('services.smtp.username');
        $this->smtpPassword = config('services.smtp.password');
        $this->smtpEncryption = config('services.smtp.encryption');
    }

    public function sendEmails(array $destinations, array $data)
    {
        $count = 0;
        $status = null;

        foreach ($destinations as $destination) {
            $status = $this->sendEmail($destination, $data) ? 'sent' : 'unsent';
            $count++;
        }

        $errorInfo = $count > 0 ? $count . ' emails sent' : 'Exception';
        // Log or handle the information as needed

        return $errorInfo;
    }

    protected function sendEmail($destination, array $data)
    {
        try {
            Mail::send([], [], function ($message) use ($destination, $data) {
                $message->to($destination)
                        ->subject($data['subject'])
                        ->setBody($data['body'], 'text/html')
                        ->from($data['from_address'], $data['from_name']);
            });

            return true;
        } catch (\Exception $e) {
            // Log error or handle exception
            return false;
        }
    }
}
