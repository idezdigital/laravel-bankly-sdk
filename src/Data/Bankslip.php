<?php

namespace Idez\Bankly\Data;

use DateTime;
use Exception;
use Idez\Bankly\Data\Webhooks\Amount;
use Psr\Http\Message\ResponseInterface;

class Bankslip extends Data
{
    public Account $account;
    public string $authenticationCode;
    public ?DateTime $updatedAt;
    public ?string $ourNumber;
    public ?string $digitable;
    public ?string $status;
    public ?string $document;
    public ?Amount $amount;
    public ?Amount $minimumAmount;
    public ?DateTime $dueDate;
    public ?DateTime $closePayment;
    public ?DateTime $emissionDate;
    public ?string $type;
    public ?Person $payer;
    public ?Person $recipientFinal;
    public ?Person $recipientOrigin;

    /**
     * @var array
     */
    public array $payments;

    /**
     * @throws Exception
     */
    public function __construct(array|ResponseInterface $data = [])
    {
        $data['account'] = new Account($data['account']);
        $data['amount'] = isset($data['amount']) ? new Amount($data['amount']) : null;
        $data['minimumAmount'] = isset($data['minimumAmount']) ? new Amount($data['minimumAmount']) : null;
        $data['updatedAt'] = isset($data['updatedAt']) ? new DateTime($data['updatedAt']) : null;
        $data['dueDate'] = isset($data['dueDate']) ? new DateTime($data['dueDate']) : null;
        $data['closePayment'] = isset($data['closePayment']) ? new DateTime($data['closePayment']) : null;
        $data['emissionDate'] = isset($data['emissionDate']) ? new DateTime($data['emissionDate']) : null;
        $data['payer'] = isset($data['payer']) ? new Person($data['payer']) : null;
        $data['recipientFinal'] = isset($data['recipientFinal']) ? new Person($data['recipientFinal']) : null;
        $data['recipientOrigin'] = isset($data['recipientOrigin']) ? new Person($data['recipientOrigin']) : null;



        parent::__construct($data);
    }
}
