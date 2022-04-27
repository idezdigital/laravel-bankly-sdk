<?php

it('should identify CPF key type', function (string $cpf) {
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($cpf);
    expect($pixKeyType)
        ->toBeInstanceOf(\Idez\Bankly\Enums\DictKeyType::class)
        ->toBe(\Idez\Bankly\Enums\DictKeyType::CPF);
})->with('cpfs');

it('should identify EMAIL key type', function () {
    $email = \Pest\Faker\faker()->email;
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($email);

    expect($pixKeyType)
        ->toBeInstanceOf(\Idez\Bankly\Enums\DictKeyType::class)
        ->toBe(\Idez\Bankly\Enums\DictKeyType::Email);
});

it('should identify CNPJ key type', function (string $cnpj) {
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($cnpj);

    expect($pixKeyType)
        ->toBeInstanceOf(\Idez\Bankly\Enums\DictKeyType::class)
        ->toBe(\Idez\Bankly\Enums\DictKeyType::CNPJ);
})->with('cnpjs');

it('should identify EVP key type', function () {
    $evp = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($evp);

    expect($pixKeyType)
        ->toBeInstanceOf(\Idez\Bankly\Enums\DictKeyType::class)
        ->toBe(\Idez\Bankly\Enums\DictKeyType::EVP);
});

it('should identify Phone key type', function (string $phone) {
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($phone);

    expect($pixKeyType)
        ->toBeInstanceOf(\Idez\Bankly\Enums\DictKeyType::class)
        ->toBe(\Idez\Bankly\Enums\DictKeyType::Phone);
})->with('phones');

it('should throws exception if not identify pix key type', function () {
    $invalidValue = Str::random();
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($invalidValue);
})->throws(\Idez\Bankly\Exceptions\InvalidDictKeyTypeException::class);

it('should clean phone number', function (string $phone) {
    $cleanedPhone = Idez\Bankly\Utils\Dict::cleanMask($phone, \Idez\Bankly\Enums\DictKeyType::Phone);
    expect($cleanedPhone)->toBe('11999999999');
})->with('phones');

it('should clean cpf', function (string $cpf) {
    $cleanedPhone = Idez\Bankly\Utils\Dict::cleanMask($cpf, \Idez\Bankly\Enums\DictKeyType::CPF);
    expect($cleanedPhone)->toBe('08697420008');
})->with('cpfs');

it('should clean cnpj', function (string $cnpj) {
    $cleanedPhone = Idez\Bankly\Utils\Dict::cleanMask($cnpj, \Idez\Bankly\Enums\DictKeyType::CNPJ);
    expect($cleanedPhone)->toBe('20129010000139');
})->with('cnpjs');

it('should not modify evp after clean mask', function () {
    $evp = \Ramsey\Uuid\Uuid::uuid4()->toString();
    $cleanedPhone = Idez\Bankly\Utils\Dict::cleanMask($evp, \Idez\Bankly\Enums\DictKeyType::EVP);
    expect($cleanedPhone)->toBe($evp);
});

it('should not modify email after clean mask', function () {
    $email = \Pest\Faker\faker()->email;
    $cleanedPhone = Idez\Bankly\Utils\Dict::cleanMask($email, \Idez\Bankly\Enums\DictKeyType::Email);
    expect($cleanedPhone)->toBe($email);
});

dataset('cpfs', ['cpf_without_mask' => '08697420008','cpf_with_mask' => '086.974.200-08']);
dataset('cnpjs', ['cnpj_with_mask' => '20.129.010/0001-39', 'cnpj_without_mask' => '20129010000139']);
dataset('phones', [
    'phone_with_mask' => '11 99999-9999',
    'phone_without_mask' => '11999999999',
    'phone_with_ddd_mask' => '(11) 99999-9999',
    'phone_with_ddi' => '+5511999999999',
]);
