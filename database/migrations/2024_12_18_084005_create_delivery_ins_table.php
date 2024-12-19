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
        Schema::create('delivery_ins', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('sender');
            $table->string('receiver');
            $table->string('received_date');
            $table->string('received_by');
            $table->string('photo');
            $table->foreignId('branch_id')->constrained('branch_offices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_ins');
    }
};
