<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medication', function (Blueprint $table) {
            $table->id();
            $table->string('age_between')->comment('age should year not month');
            $table->string('dosage');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('medicine_list_id')->index();
            $table->unsignedBigInteger('symptom_id')->index();
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
        Schema::dropIfExists('medication');
    }
}
