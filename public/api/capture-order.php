<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

require_method('POST');

$input = input_json();
$paypalOrderId = trim((string) ($input['paypal_order_id'] ?? ''));
$localId = trim((string) ($input['local_id'] ?? ''));

if ($paypalOrderId === '' || $localId === '') {
    json_response(['ok' => false, 'error' => 'Missing PayPal order id or local order id.'], 422);
}

try {
    $capture = (new PayPalClient())->captureOrder($paypalOrderId);
    $status = $capture['status'] ?? '';
    if ($status !== 'COMPLETED') {
        json_response(['ok' => false, 'error' => 'Payment was not completed.'], 409);
    }

    $content = storage()->read('content.json', []);
    $provisioner = new Provisioning();
    $updatedOrder = null;

    storage()->update('orders.json', function (&$data) use ($localId, $paypalOrderId, $capture, $content, $provisioner, &$updatedOrder) {
        foreach ($data['orders'] as &$order) {
            if (($order['local_id'] ?? '') !== $localId || ($order['paypal_order_id'] ?? '') !== $paypalOrderId) {
                continue;
            }

            $package = $provisioner->buildPackage($order, $content);
            $order['status'] = 'paid';
            $order['paypal_capture_id'] = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
            $order['paypal_capture'] = $capture;
            $order['provisioning'] = $package;
            $order['updated_at'] = gmdate('c');
            $updatedOrder = $order;
            return $order;
        }
        return null;
    }, ['orders' => []]);

    if (!$updatedOrder) {
        json_response(['ok' => false, 'error' => 'Order was captured by PayPal but not found locally. Check admin logs.'], 500);
    }

    $provisioner->dispatch($updatedOrder, $updatedOrder['provisioning']);

    json_response([
        'ok' => true,
        'order' => [
            'local_id' => $updatedOrder['local_id'],
            'status' => $updatedOrder['status'],
            'provisioning' => $updatedOrder['provisioning'],
        ],
    ]);
} catch (Throwable $e) {
    log_event('paypal_capture_error', ['message' => $e->getMessage(), 'paypal_order_id' => $paypalOrderId, 'local_id' => $localId]);
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
