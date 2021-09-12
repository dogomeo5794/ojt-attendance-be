<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->string('user_system_id', 20)->unique();
            // $table->string('firstname', 30);
            // $table->string('middlename', 30);
            // $table->string('lastname', 30);
            // $table->string('street')->nullable();
            // $table->string('barangay')->nullable();
            // $table->string('city')->nullable();
            // $table->string('province')->nullable();
            // $table->string('region')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 64);
            $table->enum('role', ["uam-admin", "clinic-staff", "user"]);
            $table->enum('user_type', ["admin", "staff", "teacher", "student"]);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
