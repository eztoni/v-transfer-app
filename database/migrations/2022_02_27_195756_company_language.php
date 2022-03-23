<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompanyLanguage extends Migration
{
    public function up()
    {
        Schema::create('company_language', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained();
            $table->foreignId('language_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_language');
    }
}
