<?php

namespace Idez\Bankly\Resources;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Discount extends Resource
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