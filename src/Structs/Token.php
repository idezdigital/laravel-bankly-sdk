<?php

namespace Idez\Bankly\Structs;

class Token extends Struct
{
    public string $token_type = 'bearer';
    public string $access_token;
    public ?string $scope;
    public ?string $expires_in;
    public ?string $claims = 'company_key';
}
