<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

$paypal = new PayPalClient();

json_response([
    'ok' => true,
    'content' => storage()->read('content.json', []),
    'settings' => storage()->read('settings.json', []),
    'paypal' => $paypal->publicConfig(),
]);
