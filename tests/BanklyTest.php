<?php

it('should returns pix client', function () {
    $bankly = (new Idez\Bankly\Bankly(authenticate: false))->pix();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\PixClient::class);
});

it('should returns transfer client', function () {
    $bankly = (new Idez\Bankly\Bankly(authenticate: false))->transfer();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\TransferClient::class);
});

it('should returns account client', function () {
    $bankly = (new Idez\Bankly\Bankly(authenticate: false))->account();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\AccountClient::class);
});

it('should returns bankslip client', function () {
    $bankly = (new Idez\Bankly\Bankly(authenticate: false))->bankslip();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\BankSlipClient::class);
});

