<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_information', function (Blueprint $table) {
            $table->id();
            $table->string('company_id', 20)->unique();
            $table->string('firstname', 30);
            $table->string('middlename', 30);
            $table->string('lastname', 30);
            $table->date('birthday');
            $table->string('contact', 15);
            $table->string('address', 100)->nullable();
            $table->unsignedBigInteger('user_id')->index();
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
        Schema::dropIfExists('staff_information');
    }
}
