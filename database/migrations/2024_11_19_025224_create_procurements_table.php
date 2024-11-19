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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->string('name');
            $table->string('user_name');
            $table->enum('status', ['proses-anggaran', 'proses-pengadaan', 'penerbitan-po', 'sudah-diterima']);
            $table->date('process_date')->nullable();
            $table->enum('invoice_status', ['belum-invoice', 'sudah-invoice']);
            $table->date('invoice_date')->nullable();
            $table->enum('payment_status', ['belum-dibayar', 'sudah-dibayar']);
            $table->date('payment_date')->nullable();
            $table->foreignId('branch_id')->constrained('branch_offices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
