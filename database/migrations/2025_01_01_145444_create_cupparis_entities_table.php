<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupparis_entities', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('model_class');
            $table->string('informazioni');
            $table->nullableTimestamps();
            $table->nullableOwnerships();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupparis_entities');
    }
};
