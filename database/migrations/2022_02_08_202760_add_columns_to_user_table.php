<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUserTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->default('Area 52')->after('password');
            $table->string('zip')->default('52')->after('city');
            $table->string('oib')->unique()->after('zip');

            $table->foreignIdFor(\App\Models\Company::class)->constrained()->after('id');
            $table->foreignIdFor(\App\Models\Owner::class)->nullable()->after('company_id');
            $table->foreignIdFor(\App\Models\Destination::class)->nullable()->after('owner_id');

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
