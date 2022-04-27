<?php

namespace Idez\Bankly\Utils;

use Idez\Bankly\Enums\DictKeyType;
use Idez\Bankly\Exceptions\InvalidDictKeyTypeException;
use Illuminate\Support\Str;

class Dict
{
    /**
     * @param string $key
     * @return DictKeyType
     * @throws InvalidDictKeyTypeException
     */
    public static function identifyDictKeyType(string $key): DictKeyType
    {
        return match (true) {
            (bool) preg_match("/^\(?(\+55)?(?:[14689][1-9]|2[12478]|3[1234578]|5[1345]|7[134579])\)? ?(?:[2-8]|9[1-9])[0-9]{3}\-?[0-9]{4}$/", $key) => DictKeyType::Phone,
            (bool) preg_match("/[0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\/?[0-9]{4}\-?[0-9]{2}/", $key) => DictKeyType::CNPJ,
            (bool) preg_match("/[0-9]{3}(\.)?[0-9]{3}(\.)?[0-9]{3}(\-)?[0-9]{2}$/i", $key) => DictKeyType::CPF,
            (bool) filter_var($key, FILTER_VALIDATE_EMAIL) => DictKeyType::Email,
            Str::isUuid($key) => DictKeyType::EVP,


            default => throw new InvalidDictKeyTypeException("Key {$key} invalid type.")
        };

    }

    /**
     * @throws InvalidDictKeyTypeException
     */
    public static function applyMask(string $key): string
    {
        $type = self::identifyDictKeyType($key);

        return match($type){
            DictKeyType::CPF => preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", self::cleanMask($key)),
            DictKeyType::CNPJ => preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", self::cleanMask($key)),
            DictKeyType::Phone => preg_replace("/(\d{3})(\d{2})(\d{5})(\d{4})/", "\$1 (\$2) \$3-\$4", self::cleanMask($key)),
            default => $key
        };
    }

    /**
     * @throws InvalidDictKeyTypeException
     */
    public static function cleanMask(string $key, ?DictKeyType $type = null): string
    {
        $type ??= self::identifyDictKeyType($key);

        return match ($type) {
            DictKeyType::CPF, DictKeyType::CNPJ, DictKeyType::Phone => preg_replace('/\D/', '', $key),
            default => $key
        };
    }
}
