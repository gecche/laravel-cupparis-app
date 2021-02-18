<?php

use Illuminate\Database\Migrations\Migration;
use Gecche\Breeze\Database\Schema\Blueprint;

class CreateDatafilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('datafiles', function(Blueprint $table)
		{
            			$table->increments('id');
			$table->integer('datafile_id')->unsigned();
            $table->text('datafile_sheet')->nullable();
            $table->string('datafile_type')->nullable();
			$table->text('data')->nullable();
			$table->nullableTimestamps();
			$table->nullableOwnerships();
            $table->unique(['datafile_id', 'datafile_type']);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('datafiles');
	}

}
