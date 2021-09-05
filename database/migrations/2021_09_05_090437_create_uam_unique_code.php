<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUamUniqueCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uam_unique_code', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code', 16)->unique();
            $table->string('user_system_id', 20);
            $table->enum('status', ['available', 'used', 'expired'])->default('available');
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
        Schema::dropIfExists('uam_unique_code');
    }
}
