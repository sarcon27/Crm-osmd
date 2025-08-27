<?php

namespace Database\Seeders;

use App\Pipelines\CreateUserProcess\CreateCompany;
use App\Pipelines\CreateUserProcess\CreateTenant;
use App\Pipelines\CreateUserProcess\CreateThirdParty;
use App\Pipelines\CreateUserProcess\CreateUser;
use App\Pipelines\CreateUserProcess\GenerateFinanceAccounts;
use Illuminate\Database\Seeder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'tenant' => [
                'name' => 'ООО "Ромашка"',
            ],
            'user' => [
                'name' => 'Админ',
                'email' => 'admin@test.com',
                'password' => '123456',
            ],
        ];

        DB::transaction(function () use ($data) {
            app(Pipeline::class)
                ->send($data)
                ->through([
                    CreateTenant::class,
                    CreateCompany::class,
                    CreateUser::class,
                    CreateThirdParty::class,
                    GenerateFinanceAccounts::class,
                ])
                ->thenReturn();
        });

    }
}
