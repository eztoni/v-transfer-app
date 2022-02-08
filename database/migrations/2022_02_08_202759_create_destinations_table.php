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

            $table->foreignIdFor(\App\Models\Company::class);

            $table->string('name');

            $table->softDeletes();
            $table->timestamps();
        });
        \App\Models\Destination::create(
            [
                'name' =>'Poreč',
                'company_id' => 1,

            ]
        );

    }

    public function down()
    {
        Schema::dropIfExists('destinations');
    }
}
