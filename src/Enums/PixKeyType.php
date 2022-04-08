<?php

namespace Idez\Bankly;

enum PixKeyType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';
    case Email = 'EMAIL';
    case Phone = 'PHONE';
    case EVP = 'EVP';
}
