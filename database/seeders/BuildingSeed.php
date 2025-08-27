<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Building::factory(25)
            ->has(
                \App\Models\Section::factory()
                    ->count(3)
                    ->state(function (array $attributes, Building $building) {
                        return [
                            'tenant_id' => $building->tenant_id,
                        ];
                    })
            )
            ->create();
    }
}
