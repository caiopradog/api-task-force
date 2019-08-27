<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $groupStatus = collect(\App\Constants\UserGroupStatusConstant::getConstants());

            $table->increments('id');
            $table->enum('status', $groupStatus->toArray())->default($groupStatus->first());
            $table->string('name');
            $table->string('description')->nullable();

            $table->integer('company_id')->nullable()->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_groups');
    }
}
