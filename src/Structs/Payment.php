<?php

namespace Idez\Bankly\Structs;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Payment extends Struct
{
    public string $id;
    public float $amount;
    public string $paymentChannel;
    public DateTime $paidOutDate;

    /**
     * @throws Exception
     */
    public function __construct(array|ResponseInterface $data = [])
    {
        $data['paidOutDate'] = new DateTime($data['paidOutDate']);

        parent::__construct($data);
    }
}
