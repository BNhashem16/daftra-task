<?php

namespace App\Enums;

enum CacheKeyPrefixEnum: string
{
    case INVENTORY = 'inventory';
    case WAREHOUSE = 'warehouse';
}
