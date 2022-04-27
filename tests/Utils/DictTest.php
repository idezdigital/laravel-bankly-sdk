<?php

it('should identify CPF key type', function (string $cpf) {
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($cpf);
    expect($pixKeyType)
        ->toBeInstanceOf(\Idez\Bankly\Enums\DictKeyType::class)
        ->toBe(\Idez\Bankly\Enums\DictKeyType::CPF);
})->with(['cpf_without_mask' => '028913038650','cpf_with_mask' => '289.130.386-50']);

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
})->with(['cpf_with_mask' => '20.129.010/0001-39', 'cpf_without_mask' => '86033966000140']);

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
})->with(['phone_with_mask' => '11 99999-9999', 'phone_without_mask' => '11999999999', 'phone_with_ddd_mask' => '(11) 99999-9999', 'phone_with_ddi' => '+5511999999999']);

it('should throws exception if not identify pix key type', function () {
    $invalidValue = Str::random();
    $pixKeyType = Idez\Bankly\Utils\Dict::identifyDictKeyType($invalidValue);
})->throws(\Idez\Bankly\Exceptions\InvalidDictKeyTypeException::class);
