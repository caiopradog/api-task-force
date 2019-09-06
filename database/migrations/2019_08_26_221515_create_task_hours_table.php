<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_time', function (Blueprint $table) {
            $groupStatus = collect(\App\Constants\TasksStatusConstant::getConstants());

            $table->increments('id');
            $table->enum('status', $groupStatus->toArray())->default('Pendente');
            $table->text('comment');
            $table->integer('time');

            $table->timestamps();
            $table->softDeletes();

            $table->integer('task_id')->nullable()->unsigned();
            $table->foreign('task_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_time');
    }
}
