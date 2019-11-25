<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RewardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Reward::create([
            'name' => 'Um café grátis',
            'description' => 'Pegue um copo de café em qualquer máquina de café na empresa!',
            'price' => 10,
            'status' => \App\Constants\DefaultStatusConstant::ACTIVE,
        ]);

        App\Models\Reward::create([
            'name' => 'Dia de folga!',
            'description' => 'Você trabalhou bastante! Tire um dia de folga por conta da casa!',
            'price' => 1000000000,
            'status' => \App\Constants\DefaultStatusConstant::ACTIVE,
        ]);
    }
}
