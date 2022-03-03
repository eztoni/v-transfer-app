<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(\App\Models\Destination::class)->constrained();
            $table->foreignIdFor(\App\Models\Point::class,'starting_point_id');
            $table->foreignIdFor(\App\Models\Point::class,'ending_point_id');
            $table->string('his_code')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
