<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('score');
            $table->string('text');
            $table->integer('type');

            $table->timestamps();

            $table->integer('reward_id')->nullable()->unsigned();
            $table->integer('user_id')->nullable()->unsigned();

            $table->foreign('reward_id')->references('id')->on('rewards');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('score_flows');
    }
}
