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
        Schema::create('inbound_item_temps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_temp_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();
            $table->integer('quantity');
            $table->double('cost')->nullable();
            $table->foreignId('branch_id')->constrained('branch_offices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_item_temps');
    }
};
