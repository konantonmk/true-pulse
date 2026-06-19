<?php

declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): void
    {
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419);
            echo 'Invalid security token. Go back and try again.';
            exit;
        }
    }
}
