<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUserTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->default('Area 52');
            $table->string('zip')->default('52');
            $table->string('oib')->unique();

            $table->foreignIdFor(\App\Models\Company::class)->constrained();
            $table->foreignIdFor(\App\Models\Destination::class)->nullable();

        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('zip');
            $table->dropColumn('oib');
        });
    }
}
