<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SemanticManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'semantic-manager';
    }
}
