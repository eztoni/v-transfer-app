<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RouteTransfer extends Migration
{
    public function up()
    {
        Schema::create('route_transfer', function (Blueprint $table) {
            $table->foreignId('route_id')->constrained();
            $table->foreignId('transfer_id')->constrained();
            $table->foreignId('partner_id')->default(0);
            $table->bigInteger('price')->default(0);
            $table->bigInteger('price_round_trip')->default(0);
            $table->boolean('round_trip')->default(false);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('tax_level')->nullable();
            $table->string('calculation_type')->nullable();
            $table->integer('commission')->default(0);
            $table->integer('discount')->default(0);
            $table->timestamps();

            $table->unique(['route_id', 'transfer_id','partner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_transfer');
    }
}
