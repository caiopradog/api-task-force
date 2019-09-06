<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $groupStatus = collect(\App\Constants\DefaultStatusConstant::getConstants());

            $table->increments('id');
            $table->enum('status', $groupStatus->toArray())->default($groupStatus->first());
            $table->string('name');
            $table->text('description');
            $table->date('deadline');

            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_created_id')->nullable()->unsigned();
            $table->integer('user_updated_id')->nullable()->unsigned();
            $table->integer('user_deleted_id')->nullable()->unsigned();

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
        Schema::dropIfExists('projects');
    }
}
