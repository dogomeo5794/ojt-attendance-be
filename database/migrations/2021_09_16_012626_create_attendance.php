<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->date("attendance_date");
            $table->time("time_in_am");
            $table->time("time_out_am");
            $table->time("time_in_pm");
            $table->time("time_out_pm");
            $table->decimal("total_hours")->default(0.0);
            $table->unsignedBigInteger('office_account_id')->index();
            $table->unsignedBigInteger('student_information_id')->index();
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
        Schema::dropIfExists('attendance');
    }
}
