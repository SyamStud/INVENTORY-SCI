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
        Schema::create('inbounds', function (Blueprint $table) {
            $table->id();
            $table->string('inbound_number');
            $table->string('received_from');
            $table->string('oder_note_number');
            $table->date('date_contract')->nullable();
            $table->string('delivery_note_number')->nullable();
            $table->date('date_received');
            $table->foreignId('received_by')->nullable()->constrained('employees');
            $table->double('total_cost');
            $table->foreignId('branch_id')->constrained('branch_offices');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbounds');
    }
};
