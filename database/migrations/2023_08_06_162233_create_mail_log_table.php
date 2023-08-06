<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservationmails', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Reservation::class)->constrained();
            $table->string('from');
            $table->string('to');
            $table->enum('email_type', ['partner_confirmation', 'guest_confirmation','guest_cancellation','partner_cancellation','partner_modification','guest_modification']);
            $table->text('debug_log');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_log');
    }
};
