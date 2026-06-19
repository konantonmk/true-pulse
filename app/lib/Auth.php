<?php

declare(strict_types=1);

final class Auth
{
    public function __construct(private readonly Storage $storage)
    {
    }

    public function login(string $email, string $password): bool
    {
        $email = strtolower(trim($email));
        $users = $this->storage->read('users.json', ['users' => []]);

        foreach ($users['users'] ?? [] as $user) {
            if (strtolower($user['email'] ?? '') !== $email || empty($user['active'])) {
                continue;
            }
            if (!$this->verifyPassword($password, $user['password'] ?? [])) {
                continue;
            }

            $_SESSION['admin_user'] = [
                'email' => $user['email'],
                'name' => $user['name'] ?? 'Administrator',
                'role' => $user['role'] ?? 'admin',
            ];
            session_regenerate_id(true);
            return true;
        }

        return false;
    }

    public function user(): ?array
    {
        return $_SESSION['admin_user'] ?? null;
    }

    public function requireAdmin(): void
    {
        if (!$this->user()) {
            header('Location: /admin/');
            exit;
        }
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function hashPassword(string $password): array
    {
        $salt = random_bytes(16);
        $iterations = 210000;
        return [
            'algorithm' => 'pbkdf2_sha256',
            'iterations' => $iterations,
            'salt' => base64_encode($salt),
            'hash' => hash_pbkdf2('sha256', $password, $salt, $iterations, 64),
        ];
    }

    private function verifyPassword(string $password, array $record): bool
    {
        if (($record['algorithm'] ?? '') === 'bcrypt' && isset($record['hash'])) {
            return password_verify($password, $record['hash']);
        }

        if (($record['algorithm'] ?? '') !== 'pbkdf2_sha256') {
            return false;
        }

        $salt = base64_decode((string) ($record['salt'] ?? ''), true);
        $expected = (string) ($record['hash'] ?? '');
        $iterations = (int) ($record['iterations'] ?? 210000);
        if ($salt === false || $expected === '') {
            return false;
        }

        $actual = hash_pbkdf2('sha256', $password, $salt, $iterations, 64);
        return hash_equals($expected, $actual);
    }
}
