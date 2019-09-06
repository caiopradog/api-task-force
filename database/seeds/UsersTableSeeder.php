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
        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Caio do Prado',
            'email' => 'caio',
            'password' => Hash::make('caio123!@#'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Eduardo Fagundes',
            'email' => 'edu',
            'password' => Hash::make('edu123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Leonardo Kenji',
            'email' => 'leo',
            'password' => Hash::make('leo123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Henrique Chinen',
            'email' => 'henrique',
            'password' => Hash::make('henrique123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Naiara Freire',
            'email' => 'naiara',
            'password' => Hash::make('naiara123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::first()->name);
    }
}
