<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);
define('DATA_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'data');
define('LOG_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'logs');

$configPath = APP_PATH . DIRECTORY_SEPARATOR . 'config.php';
$config = file_exists($configPath)
    ? require $configPath
    : require APP_PATH . DIRECTORY_SEPARATOR . 'config.example.php';

date_default_timezone_set($config['timezone'] ?? 'UTC');

foreach (glob(APP_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '*.php') as $library) {
    require_once $library;
}

if (PHP_SAPI !== 'cli') {
    $sessionName = $config['security']['session_name'] ?? 'TRUEPOINT_ADMIN';
    if (session_status() === PHP_SESSION_NONE) {
        session_name($sessionName);
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        ]);
    }
}

function app_config(?string $key = null, mixed $default = null): mixed
{
    global $config;
    if ($key === null) {
        return $config;
    }

    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function storage(): Storage
{
    static $storage = null;
    if (!$storage) {
        $storage = new Storage(DATA_PATH);
    }
    return $storage;
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function input_json(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_method(string $method): void
{
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== strtoupper($method)) {
        json_response(['ok' => false, 'error' => 'Method not allowed.'], 405);
    }
}

function app_base_url(): string
{
    $configured = trim((string) app_config('base_url', ''));
    if ($configured !== '') {
        return rtrim($configured, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

function log_event(string $channel, array $payload): void
{
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0775, true);
    }
    $line = json_encode([
        'time' => gmdate('c'),
        'channel' => $channel,
        'payload' => $payload,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents(LOG_PATH . DIRECTORY_SEPARATOR . 'app.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}
