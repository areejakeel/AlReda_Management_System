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
        Schema::table('reservations', function (Blueprint $table) {
            $table
                ->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('reservation_state_id')
                ->references('id')
                ->on('reservation_states')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('reservation_type_id')
                ->references('id')
                ->on('reservation_types')
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
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['reservation_state_id']);
            $table->dropForeign(['reservation_type_id']);
        });
    }
};
