<?php

beforeEach(function () {
    Storage::fake();
    $this->cert = \Illuminate\Http\UploadedFile::fake()->create('cert.pem')->store('cert.pem');
    $this->private = \Illuminate\Http\UploadedFile::fake()->create('cert.pem')->store('cert.pem');
    $this->passphrase = 'cx@123aacx@123aacx@123aacX@123aacx@123aacx@123aacx@123aacx@123aa';
    config([
        'bankly.mTls.certificate_path' => $this->cert ,
        'bankly.mTls.private_key_path' => $this->private,
        'bankly.mTls.passphrase' => $this->passphrase,
        'bankly.env' => 'testing',
    ]);
});


// test('should create qrcode', function () {
//     $pix = new \Idez\Bankly\Clients\PixClient();
//     $qr = $pix->qrCode('test');
//     $this->assertEquals('https://api.pix.fr/v1/qrcode/test', $qr);
// });
