<?php

namespace Idez\Bankly\Data;

class Token extends Data
{
    public string $token_type = 'bearer';
    public string $access_token;
    public ?string $scope;
    public ?int $expires_in;
    public ?string $claims = 'company_key';
}
