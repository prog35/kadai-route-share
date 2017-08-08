<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('route_id')->unsigned()->index();
            $table->double('latitude', 9,6);
            $table->double('longitude', 9,6);
            $table->timestamps();
            
            // 外部キー
            $table->foreign('route_id')->references('id')->on('routes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('route_detail');
    }
}
