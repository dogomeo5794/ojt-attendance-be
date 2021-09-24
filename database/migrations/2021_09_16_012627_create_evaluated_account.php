<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluatedAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluated_account', function (Blueprint $table) {
            $table->id();
            $table->date("action_perform_date")->nullable();
            $table->enum("action_perform", ["approved", "disapproved", "pending"])->default("pending");
            $table->longText("remarks")->nullable();
            $table->unsignedBigInteger('office_account_id')->index();
            $table->unsignedBigInteger('admin_account_id')->index();
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
        Schema::dropIfExists('evaluated_account');
    }
}
