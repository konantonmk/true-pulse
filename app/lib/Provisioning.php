<?php

declare(strict_types=1);

final class Provisioning
{
    private array $settings;

    public function __construct()
    {
        $this->settings = storage()->read('settings.json', []);
    }

    public function buildPackage(array $order, array $content): array
    {
        $country = $order['customer']['country'] ?? '';
        $mountpoint = $this->setting('default_mountpoint', 'TRUEPOINT_VRS');
        foreach ($content['countries'] ?? [] as $row) {
            if (($row['code'] ?? '') === $country && !empty($row['mountpoint'])) {
                $mountpoint = $row['mountpoint'];
                break;
            }
        }

        $prefix = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $this->setting('credential_prefix', 'TP')));
        $username = $prefix . '-' . date('ymd') . '-' . strtoupper(substr($order['local_id'], -6));

        return [
            'ntrip_host' => $this->setting('ntrip_host', app_config('provisioning.ntrip_host')),
            'ntrip_port' => $this->setting('ntrip_port', app_config('provisioning.ntrip_port')),
            'ntrip_tls' => (bool) $this->setting('ntrip_tls', app_config('provisioning.ntrip_tls', false)),
            'mountpoint' => $mountpoint,
            'username' => $username,
            'password' => bin2hex(random_bytes(5)),
            'support_email' => $this->setting('support_email', app_config('provisioning.support_email')),
            'support_note' => app_config('provisioning.technical_support_note'),
            'generated_at' => gmdate('c'),
        ];
    }

    public function dispatch(array $order, array $package): void
    {
        $mailer = new Mailer();
        $to = $order['customer']['email'] ?? '';
        if ($to === '') {
            return;
        }

        $html = '<h2>Your TruePoint RTK subscription</h2>'
            . '<p>Payment has been captured for order <strong>' . htmlspecialchars($order['local_id']) . '</strong>.</p>'
            . '<p>Use the connection parameters below in your GNSS receiver NTRIP client.</p>'
            . '<table cellpadding="6" cellspacing="0" border="1">'
            . '<tr><td>NTRIP host</td><td>' . htmlspecialchars((string) $package['ntrip_host']) . '</td></tr>'
            . '<tr><td>Port</td><td>' . htmlspecialchars((string) $package['ntrip_port']) . '</td></tr>'
            . '<tr><td>Mountpoint</td><td>' . htmlspecialchars((string) $package['mountpoint']) . '</td></tr>'
            . '<tr><td>Username</td><td>' . htmlspecialchars((string) $package['username']) . '</td></tr>'
            . '<tr><td>Password</td><td>' . htmlspecialchars((string) $package['password']) . '</td></tr>'
            . '</table>'
            . '<p>' . htmlspecialchars((string) $package['support_note']) . '</p>';

        $mailer->send($to, 'TruePoint RTK subscription connection details', $html);

        $admin = app_config('admin_email', '');
        if ($admin) {
            $mailer->send($admin, 'New paid TruePoint order ' . $order['local_id'], $html);
        }
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings['provisioning'][$key] ?? app_config('provisioning.' . $key, $default);
    }
}
