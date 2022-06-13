<?php

use Idez\Bankly\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class)
    ->beforeEach(function () {
        Storage::fake();
        $this->cert = \Illuminate\Http\UploadedFile::fake()->create('cert.pem')->store('cert.pem');
        $this->private = \Illuminate\Http\UploadedFile::fake()->create('cert.pem')->store('cert.pem');
        $this->passphrase = 'cx@123aacx@123aacx@123aacX@123aacx@123aacx@123aacx@123aacx@123aa';
        config([
            'bankly.mTls.certificate_path' => $this->cert ,
            'bankly.mTls.private_key_path' => $this->private,
            'bankly.mTls.passphrase' => $this->passphrase,
            'bankly.env' => 'testing',
            'bankly.client' => 'test',
            'bankly.secret' => 'test',
            'bankly.default_scopes' => ['test'],
        ]);
    })->in(__DIR__);

expect()->extend('toBeBase64', function () {
    $this->toBeString();
    $this->toMatch('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/');
    $decoded = base64_decode($this->value, true);
    Assert::assertNotFalse($decoded);
    Assert::assertEquals($this->value, base64_encode($decoded));

    return $this;
});
