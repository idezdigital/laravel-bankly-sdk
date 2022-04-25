<?php

namespace Idez\Bankly\Structs;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Discount extends Struct
{
    public DateTime $limitDate;
    public string $type;
    public float $value;

    /**
     * @throws Exception
     */
    public function __construct(array|ResponseInterface $data = [])
    {
        $data['limitDate'] = new DateTime($data['limitDate']);

        parent::__construct($data);
    }
}
