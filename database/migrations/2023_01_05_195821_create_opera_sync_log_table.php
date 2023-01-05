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
        Schema::create('opera_sync_log', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Reservation::class);
            $table->json('opera_request')->nullable();
            $table->json('opera_response')->nullable();
            $table->enum('sync_status',\App\Services\Api\ValamarOperaApi::STATUS_ARRAY);
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('opera_sync_log');
    }
};
