<?php

namespace Idez\Bankly\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Idez\Bankly\AccountType;
use Idez\Bankly\Bankly;
use Idez\Bankly\Exceptions\BanklyDictKeyNotFoundException;
use Idez\Bankly\Exceptions\BanklyMissingAccountDataException;
use Idez\Bankly\Exceptions\BanklyPixFailedException;
use Idez\Bankly\InitializationType;
use Idez\Bankly\RefundPixReason;
use Idez\Bankly\Structs\Account;
use Idez\Bankly\Structs\DictKey;
use Idez\Bankly\Structs\PixTransfer;
use Idez\Bankly\Structs\Refund;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;

class PixClient extends BanklyClient
{
    /**
     * @throws RequestException
     */
    public function createStaticQrCode(
        string $keyType,
        string $keyValue,
        float  $amount,
        string $conciliationId,
        string $recipientName,
        string $locationCity,
        string $locationZip,
        bool   $singlePayment = false
    ): object
    {
        return $this->client()->post("/pix/qrcodes", [
            'addressingKey' => [
                'type' => $keyType,
                'value' => $keyValue,
            ],
            'conciliationId' => $conciliationId,
            'amount' => $amount,
            'recipientName' => $recipientName,
            'singlePayment' => $singlePayment,
            'location' => [
                'city' => $locationCity,
                'zipCode' => $locationZip,
            ],
        ])->throw()->object();
    }

    /**
     * @throws BanklyPixFailedException
     */
    public function executePix(Account $from, Account|DictKey $to, float $amount, string $description = '', AccountType $type = AccountType::Checking ): PixTransfer
    {
        $data = [
            'amount' => $amount,
            'description' => $description,
            'sender' => [
                'account' => [
                    'branch' => $from->branch,
                    'number' => $from->number,
                    'type' => $type
                ],
                'bank' => [
                    'ispb' => Bankly::ACESSO_ISPB,
                    'compe' => Bankly::ACESSO_COMPE,
                    'name' => Bankly::ACESSO_NAME,
                ],
                'documentNumber' => $from?->holder?->documentNumber,
                'name' => $from?->holder->name,
            ],
        ];

        $account = $to;
        $data['initializationType'] = InitializationType::Manual;

        if ($to instanceof DictKey) {
            $data['endToEndId'] = $to->endToEndId;
            $data['initializationType'] = InitializationType::Key;
        }

        if ($to instanceof Account) {
            $data['recipient'] = [
                'account' => [
                    'branch' => $account->branch,
                    'number' => $account->number,
                    'type' => $account->type,
                ],
                'bank' => [
                    'ispb' => $account->bank->ispb,
                    'name' => $account->bank->name,
                    'compe' => $account->bank->compe,
                ],
                'name' => $account?->name ?? $account?->holder?->name ?? 'Não identificado',
                'documentNumber' => $account?->document ?? $account?->holder?->documentNumber ?? $to->holder->documentNumber ?? $to->documentNumber,
            ];
        }

        $request = $this->client()->post('/pix/cash-out', $data);
        if ($request->failed()) {
            throw new BanklyPixFailedException('Failed PIX Transfer');
        }

        return new PixTransfer($request->json());
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function refundPix(
        Account $from,
        string  $authenticationCode,
        float   $amount,
        RefundPixReason  $refundCode = RefundPixReason::NotAccepted
    ): Refund
    {
        $response = $this->client()->post('/baas/pix/cash-out:refund', [
            'account' => [
                'branch' => $from->branch,
                'number' => $from->number,
                'type' => $from->type,
            ],
            'authenticationCode' => $authenticationCode,
            'amount' => $amount,
            'refundCode' => $refundCode,
        ])->throw();

        return new Refund($response->json());
    }

    /**
     * @throws BanklyDictKeyNotFoundException
     */
    public function searchDictKey(string $key, string $pixUserId): DictKey
    {
        $request = $this->client()->withHeaders([
            'x-bkly-pix-user-id' => $pixUserId,
        ])->get("/baas/pix/entries/{$key}");

        if ($request->failed()) {
            throw new BanklyDictKeyNotFoundException("Key {$key} not found in DICT.");
        }
        return new DictKey($request->json());
    }

    /**
     */
    public function createDictKey(Account $account, AccountType $type = AccountType::Checking, string $value = ''): \Illuminate\Http\Client\Response
    {
        return $this->client()->post("/baas/pix/", [
            'addressingKey' => [
                'type' => $type,
                'value' => $value,
            ],
            'account' => [
                'branch' => $account->branch,
                'number' => $account->number,
                'type' => $account->type ?? $type,
            ],
        ]);
    }

    public function listDictKeys(string $accountNumber): \Illuminate\Http\Client\Response
    {
        return $this->client()->get("/accounts/{$accountNumber}/addressing-keys");
    }
}