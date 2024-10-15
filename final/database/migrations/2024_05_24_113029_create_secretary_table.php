<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secretary', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password')->unique();
            $table->string('UserName')->unique();
            $table->date('birthdate');
            $table->enum('gender', ['female', 'male']);
            $table->string('address');
            $table->integer('phone');
            $table->binary('secretary_img')->nullable();
            $table->string('learning_grades')->default('');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('secretary');
    }
};
