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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password')->unique();
            $table->string('UserName')->unique();
            $table->enum('gender', ['female', 'male']);
            $table->string('address');
            $table->String('specialization');
            $table->integer('phone');
            $table->binary('doctor_img')->nullable();
            $table->string('learning_grades')->default('');
            $table->foreignId('clinics_id')->constrained('clinics')->onDelete('cascade');
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
        Schema::dropIfExists('doctors');
    }
};
