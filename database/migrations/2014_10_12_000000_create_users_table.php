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
          $table->increments('id');
           $table->string('fName')->nullable();
			$table->string('lName')->nullable();
             $table->integer('otp')->nullable();
            $table->string('mobile')->unique();
            $table->string('email')->unique()->nullable();
            $table->tinyInteger('isconfirmed')->default(0);
            $table->string('password')->nullable();
            $table->string('api_token', 60)->unique()->nullable()->default(null);
            $table->boolean('status')->nullable()->default(1);
            $table->rememberToken();
            $table->softDeletes();
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
