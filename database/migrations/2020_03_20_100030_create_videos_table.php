<?php

use Gecche\Breeze\Facades\Schema;
use Gecche\Breeze\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome')->nullable();
            $table->text('descrizione')->nullable();
            $table->enum('provider', ['youtube', 'vimeo'])->default('youtube');
            $table->string('link');
            $table->text('json_data');
            $table->integer('ordine')->nullable()->default(0);
            $table->string('mediable_type')->nullable();
            $table->integer('mediable_id')->unsigned()->nullable();
            $table->timestamps();
            $table->nullableOwnerships();
//            $table->unique(['mediable_type', 'mediable_id', 'ordine']);
            $table->index(['mediable_type','mediable_id','ordine']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('videos');
    }

}
