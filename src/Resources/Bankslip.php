<?php

namespace Idez\Bankly\Resources;

use DateTime;
use Exception;
use Idez\Bankly\Resources\Webhooks\Amount;
use Psr\Http\Message\ResponseInterface;

class Bankslip extends Resource
{
    public string $authenticationCode;
    public DateTime $updatedAt;
    public string $ourNumber;
    public string $digitable;
    public string $status;
    public Account $account;
    public string $document;
    public Amount $amount;
    public Amount $minimumAmount;
    public DateTime $dueDate;
    public string $closePayment;
    public string $emissionDate;
    public string $type;
    public Person $payer;
    public Person $recipientFinal;
    public Person $recipientOrigin;

    /**
     * @var array
     */
    public array $payments;

    /**
     * @throws Exception
     */
    public function __construct(array|ResponseInterface $data = [], $branch = '0001')
    {
        $data['account'] = new Account($data['account'], $branch) ?? [];
        $data['amount'] = new Amount($data['amount']) ?? [];
        $data['minimumAmount'] = new Amount($data['minimumAmount']) ?? [];
        $data['updatedAt'] = new DateTime($data['updatedAt']);
        $data['dueDate'] = new DateTime($data['dueDate']);
        $data['closePayment'] = new DateTime($data['closePayment']);
        $data['emissionDate'] = new DateTime($data['emissionDate']);
        $data['payer'] = new Person($data['payer']) ?? [];
        $data['recipientFinal'] = new Person($data['recipientFinal']) ?? [];
        $data['recipientOrigin'] = new Person($data['recipientOrigin']) ?? [];



        parent::__construct($data);
    }
}
