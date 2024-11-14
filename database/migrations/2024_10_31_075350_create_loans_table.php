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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number');
            $table->string('customer_name');
            $table->foreignId('operation_head')->constrained('employees');
            $table->foreignId('loan_officer')->constrained('employees');
            $table->foreignId('general_division')->constrained('employees');
            $table->enum('status', ['pending', 'on_loan', 'returned'])->default('pending');
            $table->json('photos')->nullable();
            $table->string('document_path')->nullable();
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
        Schema::dropIfExists('loans');
    }
};
