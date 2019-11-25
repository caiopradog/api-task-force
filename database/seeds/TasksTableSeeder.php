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

        for ($p = 0; $p < 10;$p++) {
            $project = App\Models\Project::create([
                'name' => "Projeto ".($p+1),
                'description' => $faker->paragraph,
                'status' => 'Ativo',
                'deadline' => date('Y-m-t', strtotime('+'.($p+1).' months')),
            ]);

            $epic = [];
            for ($e = 0;$e < 5;$e++) {
                $epic[] = App\Models\Epic::create([
                    'name' => "Épico ".($e+1+$p*5)." P".($p+1) ,
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
                    'name' => "Sprint ".($s+1+$p*10)." P".($p+1) ,
                    'description' => $faker->paragraph,
                    'status' => 'Ativo',
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'project_id' => $project->id
                ]);
            }
            $sprint = collect($sprint);

            $taskQtd = 50;
            $priorities = range(1,$taskQtd);
            for ($t = 0;$t < $taskQtd;$t++) {
                $priorityKey = rand(0, count($priorities) - 1);

                $priority = $priorities[$priorityKey];
                unset($priorities[$priorityKey]);
                $priorities = array_values($priorities);
                $randEpic = $epic->random();
                $randSprint = $sprint->random();

                App\Models\Task::create([
                    'name' => "Tarefa ".($t+1+$p*5)." P".($p+1)." E".($randEpic->id)." S".($randSprint->id),
                    'description' => $faker->paragraph,
//                    'status' => App\Constants\TasksStatusConstant::getConstants()->random(),
                    'status' => "Pendente",
                    'category' => App\Constants\TasksCategoryConstant::getConstants()->random(),
                    'deadline' => $faker->dateTimeBetween('+1 week', '+2 months'),
                    'time_planned' => rand(1,8)*3600,
                    'time_used' => 0,
                    'priority' => $priority,
                    'project_id' => $project->id,
                    'epic_id' => $randEpic->id,
                    'sprint_id' => $randSprint->id,
                    'user_created_id' => \App\Models\User::all()->random()->id,
//                    'dev_user_id' => \App\Models\User::all()->random()->id,
//                    'qa_user_id' => \App\Models\User::all()->random()->id
                ]);
            }
            $this->command->info("Project ".($p+1)." created");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
