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
        Schema::create('center', function (Blueprint $table) {
            $table->id();
            $table->string('center_name');
            $table->binary('center_img')->nullable();//الصورة او اللوغو تبعو
            $table->text('description')->nullable();//متل شرح عنو و شو الخدمات الي بقدمها و اقسامو و العيادات ....
            $table->integer('phone');//رقم تواصل المركز
            $table->bigInteger('balance')->default(0);
            // رابط الواتس و رابط الفيس و العنوان
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
        Schema::dropIfExists('center');
    }
};
