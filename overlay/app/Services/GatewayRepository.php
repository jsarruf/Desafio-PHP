<?php

namespace App\Services;

use App\Models\Gateway;
use Illuminate\Support\Collection;

class GatewayRepository
{
    public function activeOrderedByPriority(): Collection
    {
        return Gateway::active()->orderBy('priority')->get();
    }
}
