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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string("name", 100);
            $table->double("area")->nullable();
            $table->text("description")->nullable();
            $table->unsignedBigInteger("sector_id");
            $table->foreign("sector_id")->references("id")->on("sectors");
            $table->unsignedBigInteger("district_id");
            $table->foreign("district_id")->references("id")->on("districts");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
