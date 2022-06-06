<?php


use Idez\Bankly\Enums\DictKeyType;

it('should returns correct label', function ($expected, DictKeyType $enum) {
    $this->assertEquals($expected, $enum->label());
})->with([
    ['E-mail', DictKeyType::Email],
    ['Telefone', DictKeyType::Phone],
    ['Chave Aleatória', DictKeyType::EVP],
    ['CPF', DictKeyType::CPF],
    ['CNPJ', DictKeyType::CNPJ],
    ['Manual', DictKeyType::Manual],
]);
