<?php

return [
    'app_name' => 'TruePoint Network',
    'timezone' => 'Europe/Bucharest',
    'base_url' => '',
    'admin_email' => 'admin@example.com',
    'mail_from' => 'sales@example.com',
    'mail_from_name' => 'TruePoint Network',

    'paypal' => [
        'mode' => 'sandbox',
        'client_id' => 'REPLACE_WITH_PAYPAL_CLIENT_ID',
        'client_secret' => 'REPLACE_WITH_PAYPAL_CLIENT_SECRET',
        'webhook_id' => '',
        'currency' => 'EUR',
    ],

    'security' => [
        'session_name' => 'TRUEPOINT_ADMIN',
        'csrf_key' => 'replace-with-a-long-random-string',
    ],

    'provisioning' => [
        'mode' => 'auto_email',
        'ntrip_host' => 'caster.true-point.example',
        'ntrip_port' => 2101,
        'ntrip_tls' => false,
        'credential_prefix' => 'TP',
        'default_mountpoint' => 'TRUEPOINT_VRS',
        'support_email' => 'support@true-point.com',
        'technical_support_note' => 'Tier-1 and tier-2 technical support is routed to the TruePoint technical team in Germany.',
    ],
];
