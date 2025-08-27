<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Measure;

final class MeasureService extends BaseService
{
    public function __construct(Measure $model)
    {
        parent::__construct($model);
    }
}
