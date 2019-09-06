<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $status = collect(\App\Constants\TasksStatusConstant::getConstants());
            $category = collect(\App\Constants\TasksCategoryConstant::getConstants());

            $table->increments('id');
            $table->enum('status', $status->toArray())->default('Pendente');
            $table->enum('category', $category->toArray());
            $table->string('name');
            $table->text('description');
            $table->date('deadline');
            $table->integer('time_planned')->default(0);
            $table->integer('time_used')->default(0);
            $table->integer('priority')->unsigned();

            $table->integer('project_id')->unsigned();
            $table->integer('epic_id')->unsigned();
            $table->integer('sprint_id')->unsigned();
            $table->integer('dev_user_id')->unsigned();
            $table->integer('qa_user_id')->unsigned();
            $table->integer('user_created_id')->nullable()->unsigned();
            $table->integer('user_updated_id')->nullable()->unsigned();
            $table->integer('user_deleted_id')->nullable()->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('epic_id')->references('id')->on('epics');
            $table->foreign('sprint_id')->references('id')->on('sprints');

            $table->foreign('dev_user_id')->references('id')->on('users');
            $table->foreign('qa_user_id')->references('id')->on('users');
            $table->foreign('user_created_id')->references('id')->on('users');
            $table->foreign('user_updated_id')->references('id')->on('users');
            $table->foreign('user_deleted_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
