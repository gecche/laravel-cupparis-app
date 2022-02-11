<?php

use Illuminate\Database\Migrations\Migration;
use Gecche\Breeze\Database\Schema\Blueprint;

class CreateQueuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
            $table->integer('error')->unsigned()->default(0);
            $table->text('msg')->nullable();
            $table->string('job_type',255)->nullable();
            $table->text('input_data')->nullable();
            $table->text('output_data')->nullable();
            $table->integer('progress')->unsigned()->nullable()->default(0);
            $table->integer('user_id')->unsigned();
            $table->integer('job_id')->unsigned()->nullable()->index();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('queues');
    }

}
