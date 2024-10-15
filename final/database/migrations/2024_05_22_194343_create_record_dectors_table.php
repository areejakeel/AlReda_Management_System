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
        Schema::create('record_dectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('records_id')->constrained('records')->onDelete('cascade');
            $table->foreignId('doctors_id')->constrained('doctors')->onDelete('cascade');
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
        Schema::dropIfExists('record_dectors');
    }
};
