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
        Schema::create('outbounds', function (Blueprint $table) {
            $table->id();
            $table->string('outbound_number');
            $table->string('release_to')->nullable();
            $table->text('release_reason')->nullable();
            $table->string('request_note_number')->nullable();
            $table->string('delivery_note_number')->nullable();
            $table->date('date_released');
            $table->foreignId('approved_by')->nullable()->constrained('employees');
            $table->foreignId('released_by')->nullable()->constrained('employees');
            $table->string('received_by')->nullable();
            $table->double('total_price');
            $table->string('photo')->nullable();
            $table->string('document_path')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
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
        Schema::dropIfExists('outbounds');
    }
};
