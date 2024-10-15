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
        Schema::create('center_visits', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('userappointment_id')->constrained('user_appointments')->onDelete('cascade');
            $table->dateTime('visit_time');
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
        Schema::dropIfExists('center_visits');
    }
};
