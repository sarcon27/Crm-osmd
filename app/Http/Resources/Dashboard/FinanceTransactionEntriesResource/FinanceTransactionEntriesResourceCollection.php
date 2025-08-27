<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard\FinanceTransactionEntriesResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceTransactionEntriesResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
