<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Caio do Prado',
            'email' => 'caio.dopradogralho@gmail.com',
            'password' => Hash::make('caio123!@#'),
        ])->assignRole(\App\Models\UserGroup::first()->name);
    }
}
