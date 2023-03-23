<?php

namespace Idez\Bankly\Enums;

enum DictKeyType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';
    case Email = 'EMAIL';
    case Phone = 'PHONE';
    case EVP = 'EVP';
    case Manual = 'MANUAL';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'E-mail',
            self::Phone => 'Telefone',
            self::EVP => 'Chave Aleatória',
            self::CPF => 'CPF',
            self::CNPJ => 'CNPJ',
            self::Manual => 'Manual',
        };
    }
}
