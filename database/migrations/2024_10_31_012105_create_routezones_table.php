<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('routezones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("route_id");
            $table->unsignedBigInteger("zone_id");
            $table->foreign("route_id")->references("id")->on("routes");
            $table->foreign("zone_id")->references("id")->on("zones");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routezones');
    }
};
