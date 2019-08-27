<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UserGroupsTableSeeder::class);
         $this->call(UsersTableSeeder::class);
         $this->call(PermissionTableSeeder::class);
         $this->call(MenuTableSeeder::class);
    }
}
