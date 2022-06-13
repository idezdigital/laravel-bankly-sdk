
# Bankly (Acesso) for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/idez/laravel-bankly-sdk.svg?style=flat-square)](https://packagist.org/packages/idez/laravel-bankly-sdk)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/idezdigital/laravel-bankly-sdk/run-tests?label=tests)](https://github.com/idezdigital/laravel-bankly-sdk/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/idezdigital/laravel-bankly-sdk/Check%20&%20fix%20styling?label=code%20style)](https://github.com/idezdigital/laravel-bankly-sdk/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/idez/laravel-bankly-sdk.svg?style=flat-square)](https://packagist.org/packages/idez/laravel-bankly-sdk)
[![Test Coverage](https://raw.githubusercontent.com/idezdigital/laravel-bankly-sdk/main/badge-coverage.svg)](https://packagist.org/packages/idez/laravel-bankly-sdk)

Unnofficial PHP class to access Bankly (by Acesso) API.

## Installation

You can install the package via composer:

```bash
composer require idez/laravel-bankly-sdk
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-bankly-sdk-migrations"
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-bankly-sdk-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

```php
$bankly = new Idez\Bankly();
$qrCodeObject =  $bankly->pix()->createStaticQrCode(
        keyType: 'evp',
        keyValue: 'd8b3c33f-9bc9-4444-8716-d7a6d243e55e',
        amount: 100.00,
        conciliationId: 'kE7nFQCy5YCFRuQ4',
        recipientName: 'Idez',
        locationCity: 'Porto Alegre',
        locationZip: '57499335',
        singlePayment: false
    );
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/idezdigital/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Idez Digital](https://github.com/idezdigital)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
