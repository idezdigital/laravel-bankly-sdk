<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Bankly;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Pix\DictKey;
use Idez\Bankly\Data\Pix\StaticQrCode;
use Idez\Bankly\Data\Pix\Transfer;
use Idez\Bankly\Data\Refund;
use Idez\Bankly\Data\ValueType;
use Idez\Bankly\Enums\AccountType;
use Idez\Bankly\Enums\DictKeyType;
use Idez\Bankly\Enums\InitializationType;
use Idez\Bankly\Enums\RefundPixReason;
use Idez\Bankly\Exceptions\InvalidDictKeyTypeException;
use Idez\Bankly\Support\Dict;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;

class PixClient extends BaseClient
{
    /**
     * @param string $keyType
     * @param string $keyValue
     * @param float $amount
     * @param string $conciliationId
     * @param string $recipientName
     * @param string $locationCity
     * @param string $locationZip
     * @param bool $singlePayment
     * @return StaticQrCode
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
    ): StaticQrCode {
        $staticQrCode = $this->client()->post("/pix/qrcodes", [
            'addressingKey' => [
                'type' => $keyType,
                'value' => $keyValue,
            ],
            'conciliationId' => $conciliationId,
            'amount' => $amount,
            'recipientName' => $this->sanitize($recipientName),
            'singlePayment' => $singlePayment,
            'location' => [
                'city' => $this->sanitize($locationCity),
                'zipCode' => $locationZip,
            ],
        ])->throw()->json();

        return StaticQrCode::make($staticQrCode);
    }

    private function sanitize(string $string): string
    {
        return Str::of($string)->ascii()->replace(['.', "'", "-", "_"], "")->__toString();
    }

    /**
     * @throws RequestException
     */
    public function executePix(Account $from, Account|DictKey $to, float $amount, string $description = '', AccountType $type = AccountType::Checking): Transfer
    {
        $data = [
            'amount' => $amount,
            'description' => $description,
            'sender' => [
                'account' => [
                    'branch' => $from->branch,
                    'number' => $from->number,
                    'type' => $type,
                ],
                'bank' => [
                    'ispb' => Bankly::ACESSO_ISPB,
                    'compe' => Bankly::ACESSO_COMPE,
                    'name' => Bankly::ACESSO_NAME,
                ],
                'documentNumber' => $from->holder?->documentNumber,
                'name' => $from->holder?->name,
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
                'name' => $account->holder?->name ?? 'Não identificado',
                'documentNumber' => $account->document ?? $account->holder?->documentNumber ?? $to->holder->documentNumber,
            ];
        }

        $transfer = $this->client()->post('/pix/cash-out', $data)->throw();

        return new Transfer($transfer->json());
    }

    /**
     * @param Account $from
     * @param string $authenticationCode
     * @param float $amount
     * @param RefundPixReason $refundCode
     * @return Refund
     */
    public function refundPix(
        Account         $from,
        string          $authenticationCode,
        float           $amount,
        RefundPixReason $refundCode = RefundPixReason::NotAccepted
    ): Refund {
        $response = $this->client()->post('/baas/pix/cash-out:refund', [
            'account' => [
                'branch' => $from->branch,
                'number' => $from->number,
                'type' => $from->type,
            ],
            'authenticationCode' => $authenticationCode,
            'amount' => $amount,
            'refundCode' => $refundCode->value,
        ])->throw();

        return new Refund($response->json());
    }

    /**
     * @param string $key
     * @param string $pixUserId
     * @param DictKeyType|null $dictKeyType
     * @return DictKey
     * @throws InvalidDictKeyTypeException
     */
    public function searchDictKey(string $key, string $pixUserId, ?DictKeyType $dictKeyType = null): DictKey
    {
        $key = Dict::cleanMask($key, $dictKeyType);

        $request = $this->client()->withHeaders([
            'x-bkly-pix-user-id' => $pixUserId,
        ])->get("/baas/pix/entries/{$key}")->throw();

        return new DictKey($request->json());
    }

    /**
     * @param string $accountNumber
     * @return ValueType[]
     */
    public function listDictKeys(string $accountNumber): array
    {
        $keys = $this->client()->get("/accounts/{$accountNumber}/addressing-keys")
            ->throw()
            ->json();

        return array_map(fn ($key) => new ValueType($key), $keys);
    }
}
