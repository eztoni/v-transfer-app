<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsTable extends Migration
{
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Owner::class)->constrained();
            $table->foreignIdFor(\App\Models\Destination::class)->constrained();
            $table->text('name');
            $table->text('description')->nullable();
            $table->string('reception_email')->nullable();
            $table->string('address')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->enum('type',\App\Models\Point::TYPE_ARRAY);
            $table->string('pms_code')->nullable();
            $table->string('pms_class')->nullable();
            $table->tinyInteger('active')->default(1);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('points');
    }
}
