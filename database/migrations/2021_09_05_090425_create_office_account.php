<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_account', function (Blueprint $table) {
            $table->id();
            $table->string('company_id', 20)->unique();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birthday');
            $table->string('contact_no', 15);
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('office_detail_id')->index();
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
        Schema::dropIfExists('office_account');
    }
}
