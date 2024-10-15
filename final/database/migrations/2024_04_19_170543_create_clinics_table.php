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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id( );
            $table->string('clinic_name');//اسم العيادة مثلا قلبية سنية تجميلية    الخخخ
            $table->binary('clinic_img')->nullable();//صورة العيادة او لوغو
            $table->string('description');//شرح عن العيادة و الخدمات الي عم تقدمها
            $table->integer('price')->default(25000);
        //    // $table->foreignId('center_id')->constrained('center')
        //         ->cascadeOnDelete()
            //   ->cascadeOnUpdate();// الفورن كي تابع للمركز
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
        Schema::dropIfExists('clinics');
    }
};
