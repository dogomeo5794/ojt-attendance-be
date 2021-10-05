<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_information', function (Blueprint $table) {
            $table->id();
            $table->string('school_id', 20)->unique();
            $table->string('email')->unique();
            $table->string('region');
            $table->string('province');
            $table->string('city');
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->date('birthday');
            $table->string('contact_no', 15);
            $table->string('course_code')->nullable();
            $table->string('course_name')->nullable();
            $table->string('section')->nullable();
            $table->string('year_level')->nullable();
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
        Schema::dropIfExists('student_information');
    }
}
