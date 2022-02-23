<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgeGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('age_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Company::class)->constrained();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('age_groups');
    }
}
