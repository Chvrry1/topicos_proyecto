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
        Schema::create('routepaths', function (Blueprint $table) {
            $table->id();
            $table->double("latitude");
            $table->double("longitude");
            $table->unsignedBigInteger("vehicleroute_id");
            $table->foreign("vehicleroute_id")->references("id")->on("vehicleroutes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routepaths');
    }
};
