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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_number');
            $table->string('name');
            $table->string('serial_number');
            $table->string('brand_id');
            $table->string('calibration');
            $table->string('photo');
            $table->foreignId('branch_id')->constrained('branch_offices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrati ons.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
