<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atribui tarefas automaticamente baseado na sua data de entrega e prioridade';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param TaskService $taskService
     * @param UserService $userService
     * @return mixed
     */
    public function handle(TaskService $taskService, UserService $userService)
    {
        $tasks = $taskService->list(['status' => 'Pendente'], false)->select(['id', 'time_planned', 'category', 'deadline'])->orderBy('deadline')->orderBy('priority')->get();

        $log = PHP_EOL;
        $usersSkills = [];
        $usersCalendar = [];
        $start = date('Y-m').'-01';
        $end = date('Y-m-t');
        $userService->list()->select(['id', 'name'])->get()->each(function ($user) use (&$usersSkills, $taskService, &$usersCalendar, $start, $end) {
            $skills = $user->user_skills->pluck('level', 'skill');

            collect($skills)->each(function ($level, $skill) use ($user, &$usersSkills) {
                $usersSkills[$skill][$level][] = $user->id;
            });

            $usersCalendar[$user->id] = [];
            $taskService->list([
                'devUserId' => $user->id,
                'plannedDateStart' => $start,
                'plannedDateEnd' => $end
            ])->orderBy('start_date', 'asc')->get()->each(function ($task) use ($user, &$usersCalendar) {
                if (isset($usersCalendar[$user->id][$task->start_date])) {
                    $usersCalendar[$user->id][$task->start_date] += $task->time_planned;
                } else {
                    $usersCalendar[$user->id][$task->start_date] = $task->time_planned;
                }
            });
        });

        $usersSkills = collect($usersSkills)->map(function ($skill) {
            return collect($skill)->sortKeys();
        });

        for ($i = 0;$i < count($tasks);$i++){
            $task = $tasks[$i];
            $selectedUserId = false;
            $selectedDate = false;

            $neededSkill = collect($usersSkills[$task->category]);

            $neededSkill->each(function ($users) use ($task, $usersCalendar, &$selectedUserId, &$selectedDate, $start, $end) {
                collect($users)->shuffle()->each(function ($user) use ($task, $usersCalendar, &$selectedUserId, &$selectedDate, $start, $end) {
                    $date = $start;
                    do {
                        if (!isset($usersCalendar[$user][$date]) || (28800 - $task->time_planned - $usersCalendar[$user][$date]) >= 0) {
                            $selectedUserId = $user;
                            $selectedDate = $date;
                            return false;
                        }
                        $date = date('Y-m-d', strtotime($date.' +1 day'));
                    } while ($date != $end);
                });
                if ($selectedUserId && $selectedDate) {
                    return false;
                }
            });

            if ($selectedUserId && $selectedDate && !isset($task->start_date)) {
                if (isset($usersCalendar[$selectedUserId][$selectedDate])) {
                    $usersCalendar[$selectedUserId][$selectedDate] += $task->time_planned;
                } else {
                    $usersCalendar[$selectedUserId][$selectedDate] = $task->time_planned;
                }

                $task->dev_user_id = $selectedUserId;
                $task->start_date = $selectedDate;

                $log .= "Atribuído a tarefa de ID {$task->id} ao usuário {$selectedUserId} na data {$selectedDate}".PHP_EOL;
                $log .= "Calendário do Usuário no momento da atribuição: ".implode($usersCalendar[$selectedUserId]).PHP_EOL.PHP_EOL;

                $taskService->update($task);
            }
        }
        Log::debug($log);
    }
}
