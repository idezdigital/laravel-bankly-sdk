<?php

namespace Idez\Bankly\Data;

class Event extends Data
{
    public string $aggregateId;
    public string $type;
    public string $category;
    public string $description;
    public string $documentNumber;
    public string $bankBranch;
    public string $bankAccount;
    public string $amount;
    public string $index;
    public string $name;
    public string $timestamp;
    public string $status;
    public object|array|null $data = null;
}
