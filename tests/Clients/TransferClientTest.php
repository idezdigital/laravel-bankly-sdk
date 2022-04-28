<?php

it('should can process transfer', function () {
    Http::fake([
        '/fund-transfers' => Http::response(['authenticationCode' => Str::uuid()], 200),
    ]);

    $client = new \Idez\Bankly\Clients\TransferClient(authenticate: false);
    $p2p = $client->p2p(\Idez\Bankly\Data\Account::factory()->make(), \Idez\Bankly\Data\Account::factory()->make(), 100);

    expect($p2p)->toBeInstanceOf(\Idez\Bankly\Data\Transfer::class);
});
