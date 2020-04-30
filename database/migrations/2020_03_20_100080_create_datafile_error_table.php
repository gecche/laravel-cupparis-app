<?php

use Illuminate\Database\Migrations\Migration;
use Gecche\Breeze\Database\Schema\Blueprint;

class CreateDatafileErrorTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datafile_error', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('datafile_table_id')->unsigned()->nullable();
            $table->string('datafile_table_type')->nullable();
            $table->integer('datafile_id')->unsigned()->nullable();
            $table->string('field_name');
            $table->string('error_name');
            $table->integer('row')->unsigned()->nullable();
            $table->string('type')->default('error');
            $table->string('value')->nullable();
            $table->boolean('template')->default(0);
            $table->string('param')->nullable();
            $table->index(array('datafile_id', 'row'));
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('datafile_error');
    }

}
