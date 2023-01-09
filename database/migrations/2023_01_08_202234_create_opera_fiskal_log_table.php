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
        Schema::create('opera_fiskal_log', function (Blueprint $table) {
            $table->id();
            $table->integer('reservation_id');
            $table->enum('log_type',\App\Services\Api\ValamarFiskalizacija::LOG_TYPE_ARRAY);
            $table->json('request')->nullable();
            $table->json('response')->nullable();
            $table->string('zki')->nullable();
            $table->string('jir')->nullable();
            $table->enum('status',array('success','error'));
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
        Schema::dropIfExists('opera_fiskal_log');
    }
};
