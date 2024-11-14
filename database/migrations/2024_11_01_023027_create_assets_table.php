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
            $table->string('tag_number');
            $table->string('name');
            $table->string('brand_id');
            $table->string('serial_number');
            $table->string('color');
            $table->string('size');
            $table->enum('condition', ['baik', 'rusak']);
            $table->enum('status', ['terpakai', 'tidak terpakai']);
            $table->string('permit');
            $table->json('calibration');
            $table->string('calibration_number');
            $table->integer('calibration_interval');
            $table->date('calibration_start_date');
            $table->date('calibration_due_date');
            $table->string('calibration_institution');
            $table->string('calibration_type');
            $table->string('range');
            $table->string('correction_factor');
            $table->enum('significance', ['ya', 'tidak']);
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
