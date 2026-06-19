<?php

declare(strict_types=1);

final class Mailer
{
    public function send(string $to, string $subject, string $html, ?string $text = null): bool
    {
        $from = app_config('mail_from', 'no-reply@example.com');
        $name = app_config('mail_from_name', 'TruePoint Network');
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $name . ' <' . $from . '>',
            'Reply-To: ' . $from,
        ];

        $sent = false;
        if (function_exists('mail')) {
            $sent = @mail($to, $subject, $html, implode("\r\n", $headers));
        }

        log_event('mail', [
            'to' => $to,
            'subject' => $subject,
            'sent' => $sent,
            'text' => $text,
        ]);

        return $sent;
    }
}
