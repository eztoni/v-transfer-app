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
        Schema::table('travellers', function (Blueprint $table) {
            //Add Opera ID
            $table->string('reservation_opera_id')->nullable();
            //Add Confirmation Number
            $table->string('reservation_opera_confirmation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travellers', function (Blueprint $table) {
            //
        });
    }
};
