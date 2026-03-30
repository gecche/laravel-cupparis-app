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
        Schema::create('cupparis_entities_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id')->index();
            $table->foreign('entity_id')->references('id')->on('cupparis_entities')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nome');
            $table->string('tipo');
            $table->string('informazioni')->nullable()->default(null);
            $table->boolean('nullable')->default(1);
            $table->string('default')->nullable()->default(null);
            $table->string('index')->nullable()->default(null);
            $table->string('relazione_tabella')->nullable()->default(null);
            $table->string('relazione_campo')->nullable()->default(null);
            $table->string('on_delete')->nullable()->default(null);
            $table->string('on_update')->nullable()->default(null);

            $table->string('model_conf_search')->nullable()->default(null);
            $table->string('model_conf_list')->nullable()->default(null);
            $table->string('model_conf_edit')->nullable()->default(null);

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
        Schema::dropIfExists('cupparis_entities_fields');
    }
};
