<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_list', function (Blueprint $table) {
            $table->id();
            $table->integer('qty');
            $table->unsignedBigInteger('medicine_list_id')->index();
            $table->unsignedBigInteger('purchase_id')->index();
            $table->unsignedBigInteger('user_id')->index()->nullable();
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
        Schema::dropIfExists('purchase_list');
    }
}
