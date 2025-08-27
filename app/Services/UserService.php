<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\UserMapper;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;

final class UserService extends BaseService
{
    private UserMapper $userMapper;

    public function __construct(User $model, UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
        parent::__construct($model);
    }

    public function create(DataTransferObject $dto): Model
    {
        $model = $this->model;

        DB::beginTransaction();
        try {
            $data = $this->userMapper->map($dto);
            $model->fill($data);

            if (! $model->save()) {
                throw new Exception('Can`t save model');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $model;
    }
}
