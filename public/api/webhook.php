<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$body = file_get_contents('php://input') ?: '';
$headers = function_exists('getallheaders') ? getallheaders() : [];

try {
    $paypal = new PayPalClient();
    if (!$paypal->verifyWebhook($headers, $body)) {
        log_event('paypal_webhook_rejected', ['headers' => $headers, 'body' => $body]);
        json_response(['ok' => false, 'error' => 'Webhook verification failed.'], 400);
    }

    $event = json_decode($body, true) ?: [];
    log_event('paypal_webhook', $event);

    $type = $event['event_type'] ?? '';
    if ($type === 'PAYMENT.CAPTURE.COMPLETED') {
        $resource = $event['resource'] ?? [];
        $invoiceId = $resource['invoice_id'] ?? null;
        if ($invoiceId) {
            storage()->update('orders.json', function (&$data) use ($invoiceId, $resource) {
                foreach ($data['orders'] as &$order) {
                    if (($order['invoice_id'] ?? '') === $invoiceId && ($order['status'] ?? '') !== 'paid') {
                        $order['status'] = 'paid_webhook';
                        $order['paypal_capture_id'] = $resource['id'] ?? null;
                        $order['updated_at'] = gmdate('c');
                    }
                }
                return true;
            }, ['orders' => []]);
        }
    }

    json_response(['ok' => true]);
} catch (Throwable $e) {
    log_event('paypal_webhook_error', ['message' => $e->getMessage()]);
    json_response(['ok' => false, 'error' => 'Webhook processing failed.'], 500);
}
