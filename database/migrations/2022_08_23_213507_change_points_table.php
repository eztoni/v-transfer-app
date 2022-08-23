<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePointsTable extends Migration
{
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->text('internal_name')->after('name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('points', function (Blueprint $table) {

        });
    }
}
