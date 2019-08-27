<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('epics', function (Blueprint $table) {
            $groupStatus = collect(\App\Constants\DefaultStatusConstant::getConstants());

            $table->increments('id');
            $table->enum('status', $groupStatus->toArray())->default($groupStatus->first());
            $table->string('name');
            $table->text('description');
            $table->integer('project_id')->unsigned();

            $table->integer('user_created_id')->nullable()->unsigned();
            $table->integer('user_updated_id')->nullable()->unsigned();
            $table->integer('user_deleted_id')->nullable()->unsigned();

            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
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
        Schema::dropIfExists('epics');
    }
}
