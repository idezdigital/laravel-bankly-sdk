<?php

namespace Idez\Bankly\Enums;

enum RefundPixReason: string
{
    case Fraud = 'FR01';
    case Duplicated = 'AM05';
    case NotAccepted = 'MD06';
    case Dispute = 'RUTA';
    case IncorrectAmount = 'AM09';

    public function availables(): array
    {
        return [
            self::Fraud,
            self::NotAccepted,
        ];
    }
}
