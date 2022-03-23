<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contact');
            $table->string('email');

            $table->foreignIdFor(\App\Models\Country::class)->constrained();
            $table->string('city');
            $table->string('zip');

            $table->softDeletes();
            $table->timestamps();
        });



    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
