<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string("image_path", 100);
            $table->string("image_type", 10)->nullable();
            $table->string("image_name", 100)->nullable();
            $table->enum('set_as', ['profile', 'attachment', 'qrcode'])->default('attachment');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('image_id')->index();
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
        Schema::dropIfExists('images');
    }
}
