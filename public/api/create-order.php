<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

require_method('POST');

$input = input_json();
$content = storage()->read('content.json', []);
$settings = storage()->read('settings.json', []);
$plans = $content['plans'] ?? [];
$plan = null;

foreach ($plans as $candidate) {
    if (($candidate['id'] ?? '') === ($input['plan_id'] ?? '')) {
        $plan = $candidate;
        break;
    }
}

if (!$plan) {
    json_response(['ok' => false, 'error' => 'Unknown subscription plan.'], 422);
}

$customer = [
    'name' => trim((string) ($input['customer']['name'] ?? '')),
    'email' => trim((string) ($input['customer']['email'] ?? '')),
    'country' => strtoupper(trim((string) ($input['customer']['country'] ?? ''))),
    'receiver' => trim((string) ($input['customer']['receiver'] ?? '')),
    'notes' => trim((string) ($input['customer']['notes'] ?? '')),
];

if ($customer['name'] === '' || !filter_var($customer['email'], FILTER_VALIDATE_EMAIL) || $customer['country'] === '') {
    json_response(['ok' => false, 'error' => 'Name, valid email, and service country are required.'], 422);
}

$allowedCountries = array_column($content['countries'] ?? [], 'code');
if (!in_array($customer['country'], $allowedCountries, true)) {
    json_response(['ok' => false, 'error' => 'Selected country is not in the current service footprint.'], 422);
}

$localId = 'TP-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(3)));
$invoice = $localId;
$amount = (float) $plan['price'];
$taxRate = (float) ($settings['checkout']['tax_rate'] ?? 0);
$tax = round($amount * $taxRate, 2);
$total = round($amount + $tax, 2);

$order = [
    'local_id' => $localId,
    'invoice_id' => $invoice,
    'status' => 'created',
    'plan_id' => $plan['id'],
    'plan_name' => $plan['name'] ?? $plan['name_key'],
    'amount' => $total,
    'subtotal' => $amount,
    'tax' => $tax,
    'currency' => app_config('paypal.currency', 'EUR'),
    'customer' => $customer,
    'paypal_order_id' => null,
    'paypal_capture_id' => null,
    'provisioning' => null,
    'created_at' => gmdate('c'),
    'updated_at' => gmdate('c'),
];

try {
    $paypalOrder = (new PayPalClient())->createOrder($order);
    $order['paypal_order_id'] = $paypalOrder['id'] ?? null;
    $order['status'] = 'paypal_created';

    storage()->update('orders.json', function (&$data) use ($order) {
        $data['orders'][] = $order;
        return $order;
    }, ['orders' => []]);

    json_response([
        'ok' => true,
        'local_id' => $localId,
        'paypal_order_id' => $order['paypal_order_id'],
    ]);
} catch (Throwable $e) {
    log_event('paypal_create_error', ['message' => $e->getMessage(), 'order' => $order]);
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
