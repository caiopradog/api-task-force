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
            'name' => 'Admin',
            'email' => 'adm',
            'password' => Hash::make('adm123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Caio do Prado',
            'email' => 'caio',
            'total_score' => 10000,
            'current_score' => 1000,
            'password' => Hash::make('caio123!@#'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::where('name', 'Desenvolvedor')->first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Eduardo Fagundes',
            'email' => 'edu',
            'total_score' => 10000,
            'current_score' => 1000,
            'password' => Hash::make('edu123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::where('name', 'Desenvolvedor')->first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Leonardo Kenji',
            'email' => 'leo',
            'total_score' => 10000,
            'current_score' => 1000,
            'password' => Hash::make('leo123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::where('name', 'Desenvolvedor')->first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Henrique Chinen',
            'email' => 'henrique',
            'total_score' => 10000,
            'current_score' => 1000,
            'password' => Hash::make('henrique123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::where('name', 'Desenvolvedor')->first()->name);

        App\Models\User::create([
            'user_group_id' => '1',
            'name' => 'Naiara Freire',
            'email' => 'naiara',
            'total_score' => 10000,
            'current_score' => 1000,
            'password' => Hash::make('naiara123'),
            'api_token' => \Illuminate\Support\Str::random(80)
        ])->assignRole(\App\Models\UserGroup::where('name', 'Desenvolvedor')->first()->name);
    }
}
