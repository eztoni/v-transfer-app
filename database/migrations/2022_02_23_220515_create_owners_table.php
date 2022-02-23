<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnersTable extends Migration
{
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Company::class);
            $table->string('name');

            $table->timestamps();
        });

        \App\Models\Owner::create(
            [
                'name' =>'Valamar Rivijera',
                'company_id' => 1,
            ]
        );
        \App\Models\Owner::create(
            [
                'name' =>'Imperial Rab',
                'company_id' => 1,
            ]
        );
    }

    public function down()
    {
        Schema::dropIfExists('owners');
    }
}
