<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string("name", 100);
            $table->string("code", 100)->nullable();
            $table->string("plate", 10);
            $table->string("year", 10);
            $table->integer("occupant_capacity");
            $table->double("load_capacity");
            $table->text("description")->nullable();
            $table->integer("status");
            $table->unsignedBigInteger("brand_id");
            $table->unsignedBigInteger("model_id");
            $table->unsignedBigInteger("type_id");
            $table->unsignedBigInteger("color_id");
            $table->foreign("brand_id")->references("id")->on("brands");
            $table->foreign("model_id")->references("id")->on("brandmodels");
            $table->foreign("type_id")->references("id")->on("vehicletypes");
            $table->foreign("color_id")->references("id")->on("vehiclecolors");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
