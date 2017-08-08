<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_route', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('route_id')->unsigned()->index();
            $table->smallinteger('type')->index();
            $table->timestamps();
            
            // 外部キー
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('route_id')->references('id')->on('routes');
            // 一意制約
            $table->unique(['user_id','route_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_route');
    }
}
