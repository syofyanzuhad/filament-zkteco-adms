<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Commands;

use Illuminate\Console\Command;

class FilamentZktecoAdmsCommand extends Command
{
    public $signature = 'filament-zkteco-adms';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
