<?php
// config for Idez/Bankly
return [
    'client' => env('BANKLY_CLIENT'), /* @deprecated */
    'secret' => env('BANKLY_SECRET'), /* @deprecated */
    'env' => env('APP_ENV', 'staging'),
    'webhooks' => [
        'public_key' => env('BANKLY_WEBHOOK_PUBLIC_KEY'),
        'hmac_salt' => env('BANKLY_WEBHOOK_HMAC_SALT'),
    ],
    'branch' => env('BANKLY_BRANCH', '0001'),
    'default_scopes' => env('BANKLY_SCOPES', []),
    'company_key' => env('BANKLY_COMPANY_KEY'),
    'mTls' => [
        'certificate_path' => env('BANKLY_MTLS_CERT_PATH'),
        'private_key_path' => env('BANKLY_MTLS_PRIVATE_KEY_PATH'),
        'passphrase' => env('BANKLY_MTLS_PASSPHRASE'),
    ],
    'oauth2' => [
        'client_id' => env('BANKLY_OAUTH2_CLIENT_ID'),
    ]
];
