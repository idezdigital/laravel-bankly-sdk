<?php
// config for Idez/Bankly
return [
    'client' => env('BANKLY_CLIENT', ''),
    'secret' => env('BANKLY_SECRET', ''),
    'env' => env('APP_ENV', 'staging'),
    'hmac_salt' => env('BANKLY_HMAC_SALT'),
    'webhooks' => [
        'public_key' => env('BANKLY_WEBHOOK_PUBLIC_KEY'),
    ],
    'branch' => env('BANKLY_BRANCH', '0001'),
    'default_scopes' => env('BANKLY_SCOPES'),
    'company_key' => env('BANKLY_COMPANY_KEY'),

    'oauth2' => [
        'subject_dn' => env('BANKLY_OAUTH2_SUBJECT_DN'),
        'password' => env('BANKLY_OAUTH2_PASSWORD')
    ]

];
