<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UsersSkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Caio
        App\Models\UserSkill::create([
            'user_id' => 1,
            'skill' => 'Front-End',
            'level' => 2
        ]);
        App\Models\UserSkill::create([
            'user_id' => 1,
            'skill' => 'Back-End',
            'level' => 1
        ]);
        App\Models\UserSkill::create([
            'user_id' => 1,
            'skill' => 'Analista',
            'level' => 5
        ]);
        App\Models\UserSkill::create([
            'user_id' => 1,
            'skill' => 'Infraestrutura',
            'level' => 3
        ]);
        App\Models\UserSkill::create([
            'user_id' => 1,
            'skill' => 'Design',
            'level' => 4
        ]);

        // Edu
        App\Models\UserSkill::create([
            'user_id' => 2,
            'skill' => 'Front-End',
            'level' => 2
        ]);
        App\Models\UserSkill::create([
            'user_id' => 2,
            'skill' => 'Back-End',
            'level' => 4
        ]);
        App\Models\UserSkill::create([
            'user_id' => 2,
            'skill' => 'Analista',
            'level' => 3
        ]);
        App\Models\UserSkill::create([
            'user_id' => 2,
            'skill' => 'Infraestrutura',
            'level' => 1
        ]);
        App\Models\UserSkill::create([
            'user_id' => 2,
            'skill' => 'Design',
            'level' => 4
        ]);

        // Leo
        App\Models\UserSkill::create([
            'user_id' => 3,
            'skill' => 'Front-End',
            'level' => 3
        ]);
        App\Models\UserSkill::create([
            'user_id' => 3,
            'skill' => 'Back-End',
            'level' => 4
        ]);
        App\Models\UserSkill::create([
            'user_id' => 3,
            'skill' => 'Analista',
            'level' => 2
        ]);
        App\Models\UserSkill::create([
            'user_id' => 3,
            'skill' => 'Infraestrutura',
            'level' => 5
        ]);
        App\Models\UserSkill::create([
            'user_id' => 3,
            'skill' => 'Design',
            'level' => 1
        ]);

        // Henrique
        App\Models\UserSkill::create([
            'user_id' => 4,
            'skill' => 'Front-End',
            'level' => 5
        ]);
        App\Models\UserSkill::create([
            'user_id' => 4,
            'skill' => 'Back-End',
            'level' => 3
        ]);
        App\Models\UserSkill::create([
            'user_id' => 4,
            'skill' => 'Analista',
            'level' => 1
        ]);
        App\Models\UserSkill::create([
            'user_id' => 4,
            'skill' => 'Infraestrutura',
            'level' => 4
        ]);
        App\Models\UserSkill::create([
            'user_id' => 4,
            'skill' => 'Design',
            'level' => 2
        ]);

        // Naiara
        App\Models\UserSkill::create([
            'user_id' => 5,
            'skill' => 'Front-End',
            'level' => 1
        ]);
        App\Models\UserSkill::create([
            'user_id' => 5,
            'skill' => 'Back-End',
            'level' => 3
        ]);
        App\Models\UserSkill::create([
            'user_id' => 5,
            'skill' => 'Analista',
            'level' => 4
        ]);
        App\Models\UserSkill::create([
            'user_id' => 5,
            'skill' => 'Infraestrutura',
            'level' => 5
        ]);
        App\Models\UserSkill::create([
            'user_id' => 5,
            'skill' => 'Design',
            'level' => 2
        ]);
    }
}
