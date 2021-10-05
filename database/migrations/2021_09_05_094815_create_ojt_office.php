<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOjtOffice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ojt_office', function (Blueprint $table) {
            $table->id();
            $table->enum('duty_status', ['active', 'terminated', 'completed'])->default('active');
            $table->longText('remarks')->nullable();
            $table->unsignedBigInteger('student_information_id')->index();
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
        Schema::dropIfExists('ojt_office');
    }
}
