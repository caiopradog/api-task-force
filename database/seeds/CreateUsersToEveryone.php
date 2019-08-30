<?php

use Illuminate\Database\Seeder;

class CreateUsersToEveryone extends Seeder
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
            'name' => 'Eduardo Fagundes',
            'email' => 'edu',
            'password' => Hash::make('edu123'),
        ])->assignRole(\App\Models\UserGroup::first()->name);

        $user = App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Leonardo Kenji',
            'email' => 'leo',
            'password' => Hash::make('leo123'),
        ])->assignRole(\App\Models\UserGroup::first()->name);

        $user = App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Henrique Chinen',
            'email' => 'henrique',
            'password' => Hash::make('henrique123'),
        ])->assignRole(\App\Models\UserGroup::first()->name);

        $user = App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Naiara Freire',
            'email' => 'naiara',
            'password' => Hash::make('naiara123'),
        ])->assignRole(\App\Models\UserGroup::first()->name);
    }
}
