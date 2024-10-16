<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('value');
            $table->boolean('type');
            $table->longText('describtion');
            $table->unsignedBigInteger('wallet_id');

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
        Schema::dropIfExists('wallet_operations');
    }
};
