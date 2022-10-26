<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAndTermsToPartnersTable extends Migration
{
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->text('terms')->nullable();
        });
    }

    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            //
        });
    }
}
