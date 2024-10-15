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
        Schema::create('records', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('father_name');
                $table->string('last_name');
                $table->date('birthdate');
                $table->enum('gender', ['female', 'male']);
                $table->string('address');
                $table->integer('moblie_num');
                $table->String('Blood_type');
                $table->enum('social_status',['single','married']);
                $table->string('job');
                $table->text('Previous_Opertios');
                $table->text('AllergyToMedication');
                $table->String('Chronic_Diseases');
                $table->string('_first_name');
                $table->string('_last_name');
                $table->integer('phone');
                $table->json('X_ray')->nullable();
                $table->json('analysis')->nullable();
                $table->json('description')->nullable();
                $table->json('date')->nullable();
                $table->json('Referrals')->nullable();
              
                // description . هي ليستا من الداكاترة التي زارها مع الوقت وماذا فعل  
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade')->nullable();
               
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
        Schema::dropIfExists('records');
    }
};
