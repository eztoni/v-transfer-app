<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    public function up()
    {
        DB::unprepared(file_get_contents('database/countries.sql'));
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
