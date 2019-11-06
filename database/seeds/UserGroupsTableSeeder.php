<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userGroup = new App\Models\UserGroup();
        $userGroup->name = 'Administrador';
        $userGroup->save();

        $userGroup = new App\Models\UserGroup();
        $userGroup->name = 'Gerente';
        $userGroup->save();

        $userGroup = new App\Models\UserGroup();
        $userGroup->name = 'Desenvolvedor';
        $userGroup->save();
    }
}
