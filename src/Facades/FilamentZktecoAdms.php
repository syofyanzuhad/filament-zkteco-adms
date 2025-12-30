<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdms
 */
class FilamentZktecoAdms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdms::class;
    }
}
