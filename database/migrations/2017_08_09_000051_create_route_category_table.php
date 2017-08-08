<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->index();
            $table->integer('route_id')->unsigned()->index();
            $table->timestamps();
            
            // 外部キー
            $table->foreign('category_id')->references('id')->on('categorys');
            $table->foreign('route_id')->references('id')->on('routes');
            // 一意制約
            $table->unique(['category_id','route_id']);     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('route_category');
    }
}
