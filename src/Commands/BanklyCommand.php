<?php

namespace Idez\Bankly\Enums\Commands;

use Illuminate\Console\Command;

class BanklyCommand extends Command
{
    public $signature = 'laravel-bankly-sdk';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
