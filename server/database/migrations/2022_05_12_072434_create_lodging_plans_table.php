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
        Schema::create('lodging_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('location_lat', 8, 6);
            $table->decimal('location_lng', 9, 6);
            $table->string('location_name');
            $table->text('location_address');
            $table->dateTime('check_in_date');
            $table->dateTime('check_out_date');
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
        Schema::dropIfExists('lodging_plans');
    }
};
