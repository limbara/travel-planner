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
        Schema::create('transport_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('lat_from', 8, 6);
            $table->decimal('lng_from', 9, 6);
            $table->decimal('lat_to', 8, 6);
            $table->decimal('lng_to', 9, 6);
            $table->text('address_from');
            $table->text('address_to');
            $table->string('transportation', 255);
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
        Schema::dropIfExists('transport_plans');
    }
};
