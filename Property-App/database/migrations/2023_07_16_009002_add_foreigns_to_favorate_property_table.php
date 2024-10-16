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
        Schema::table('favorate_property', function (Blueprint $table) {
            $table
                ->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('favorate_id')
                ->references('id')
                ->on('favourites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorate_property', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['favorate_id']);
        });
    }
};
