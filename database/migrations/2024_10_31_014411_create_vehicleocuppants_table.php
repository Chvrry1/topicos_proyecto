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
        Schema::create('vehicleocuppants', function (Blueprint $table) {
            $table->id();
            $table->integer("status");
            $table->unsignedBigInteger("vehicle_id");
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("usertype_id");
            $table->foreign("vehicle_id")->references("id")->on("vehicles");
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("usertype_id")->references("id")->on("usertypes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicleocuppants');
    }
};
