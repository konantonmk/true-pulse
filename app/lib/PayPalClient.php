<?php

declare(strict_types=1);

final class PayPalClient
{
    private string $mode;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->mode = app_config('paypal.mode', 'sandbox');
        $this->clientId = (string) app_config('paypal.client_id', '');
        $this->clientSecret = (string) app_config('paypal.client_secret', '');
    }

    public function publicConfig(): array
    {
        return [
            'mode' => $this->mode,
            'clientId' => $this->clientId,
            'currency' => app_config('paypal.currency', 'EUR'),
            'configured' => $this->isConfigured(),
        ];
    }

    public function createOrder(array $order): array
    {
        $token = $this->accessToken();
        return $this->request('POST', '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $order['local_id'],
                'custom_id' => $order['local_id'],
                'invoice_id' => $order['invoice_id'],
                'description' => $order['plan_name'] . ' - TruePoint RTK Subscription',
                'amount' => [
                    'currency_code' => app_config('paypal.currency', 'EUR'),
                    'value' => number_format((float) $order['amount'], 2, '.', ''),
                ],
            ]],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        'brand_name' => 'TruePoint Network',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                    ],
                ],
            ],
        ], $token);
    }

    public function captureOrder(string $paypalOrderId): array
    {
        $token = $this->accessToken();
        return $this->request('POST', '/v2/checkout/orders/' . rawurlencode($paypalOrderId) . '/capture', [], $token);
    }

    public function verifyWebhook(array $headers, string $body): bool
    {
        $webhookId = (string) app_config('paypal.webhook_id', '');
        if ($webhookId === '') {
            return false;
        }

        $headers = array_change_key_case($headers, CASE_LOWER);
        $token = $this->accessToken();
        $payload = [
            'auth_algo' => $headers['paypal-auth-algo'] ?? '',
            'cert_url' => $headers['paypal-cert-url'] ?? '',
            'transmission_id' => $headers['paypal-transmission-id'] ?? '',
            'transmission_sig' => $headers['paypal-transmission-sig'] ?? '',
            'transmission_time' => $headers['paypal-transmission-time'] ?? '',
            'webhook_id' => $webhookId,
            'webhook_event' => json_decode($body, true),
        ];

        $result = $this->request('POST', '/v1/notifications/verify-webhook-signature', $payload, $token);
        return ($result['verification_status'] ?? '') === 'SUCCESS';
    }

    private function isConfigured(): bool
    {
        return $this->clientId !== ''
            && $this->clientSecret !== ''
            && !str_starts_with($this->clientId, 'REPLACE_')
            && !str_starts_with($this->clientSecret, 'REPLACE_');
    }

    private function accessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('PayPal credentials are not configured.');
        }

        $ch = curl_init($this->baseUrl() . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_USERPWD => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Accept-Language: en_US'],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $status >= 400) {
            throw new RuntimeException('PayPal token request failed: ' . ($error ?: $response));
        }

        $decoded = json_decode($response, true);
        if (empty($decoded['access_token'])) {
            throw new RuntimeException('PayPal token response did not include an access token.');
        }

        return $decoded['access_token'];
    }

    private function request(string $method, string $path, array $payload, string $token): array
    {
        $ch = curl_init($this->baseUrl() . $path);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $status >= 400) {
            throw new RuntimeException('PayPal API request failed: ' . ($error ?: $response));
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function baseUrl(): string
    {
        return $this->mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
