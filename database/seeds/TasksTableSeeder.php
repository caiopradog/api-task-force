<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Faker $faker
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('tasks')->truncate();
        DB::table('epics')->truncate();
        DB::table('sprints')->truncate();
        DB::table('projects')->truncate();

        for ($p = 0; $p < 5;$p++) {
            $project = App\Models\Project::create([
                'name' => $faker->sentence(3),
                'description' => $faker->paragraph,
                'status' => 'Ativo',
                'deadline' => date('Y-m-t', strtotime('+'.($p+1).' months')),
            ]);

            $epic = [];
            for ($e = 0;$e < 5;$e++) {
                $epic[] = App\Models\Epic::create([
                    'name' => $faker->sentence(3),
                    'description' => $faker->paragraph,
                    'status' => 'Ativo',
                    'project_id' => $project->id
                ]);
            }
            $epic = collect($epic);

            $sprint = [];
            for ($s = 0;$s < 10;$s++) {
                $startDate = date('Y-m-d', strtotime('next sunday +'.$s.' weeks'));
                $endDate = date('Y-m-d', strtotime($startDate.' +6 days'));
                $sprint[] = App\Models\Sprint::create([
                    'name' => $faker->sentence(3),
                    'description' => $faker->paragraph,
                    'status' => 'Ativo',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'project_id' => $project->id
                ]);
            }
            $sprint = collect($sprint);

            $priorities = range(1,50);
            for ($t = 0;$t < 50;$t++) {
                $priorityKey = rand(0, count($priorities) - 1);

                $priority = $priorities[$priorityKey];
                unset($priorities[$priorityKey]);
                $priorities = array_values($priorities);

                App\Models\Task::create([
                    'name' => $faker->sentence(3),
                    'description' => $faker->paragraph,
//                    'status' => App\Constants\TasksStatusConstant::getConstants()->random(),
                    'status' => "Pendente",
                    'category' => App\Constants\TasksCategoryConstant::getConstants()->random(),
                    'deadline' => $faker->dateTimeBetween('+1 week', '+2 months'),
                    'time_planned' => rand(1,3)*3600,
                    'time_used' => 0,
                    'priority' => $priority,
                    'project_id' => $project->id,
                    'epic_id' => $epic->random()->id,
                    'sprint_id' => $sprint->random()->id,
//                    'dev_user_id' => \App\Models\User::all()->random()->id,
//                    'qa_user_id' => \App\Models\User::all()->random()->id
                ]);
            }
            $this->command->info("Project {$p} created");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
