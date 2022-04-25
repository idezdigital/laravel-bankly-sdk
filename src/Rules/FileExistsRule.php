<?php

namespace Idez\Bankly\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FileExistsRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return Storage::exists($value);
    }

    public function message(): string
    {
        return 'The file does not exist.';
    }
}
