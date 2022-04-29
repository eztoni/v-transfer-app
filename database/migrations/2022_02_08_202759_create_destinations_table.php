<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDestinationsTable extends Migration
{
    public function up()
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignIdFor(\App\Models\Owner::class);
            $table->foreignIdFor(\App\Models\Partner::class)->nullable();

            $table->string('name');

            $table->softDeletes();
            $table->timestamps();
        });


    }

    public function down()
    {
        Schema::dropIfExists('destinations');
    }
}
