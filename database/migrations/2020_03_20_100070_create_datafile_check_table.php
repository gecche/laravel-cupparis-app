<?php

use Illuminate\Database\Migrations\Migration;
use Gecche\Breeze\Database\Schema\Blueprint;

class CreateDatafileCheckTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datafile_check', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('datafile_table_id')->unsigned()->nullable();
            $table->string('datafile_sheet')->nullable();
            $table->integer('datafile_id')->unsigned()->nullable();
            $table->string('field_name');
            $table->string('function_name');
            $table->integer('row')->unsigned()->nullable();
            $table->string('value_string')->nullable();
            $table->integer('value_int')->nullable()->unsigned();
            $table->double('value_double')->nullable();
            $table->index(array('datafile_id', 'field_name', 'value_string'));
            $table->index(array('datafile_id', 'field_name', 'value_int'));
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('datafile_check');
    }

}
