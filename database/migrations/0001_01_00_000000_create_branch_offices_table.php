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
        Schema::create('branch_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->char('province_id', 2)->charset('utf8')->collation('utf8_unicode_ci');
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->char('regency_id', 4)->charset('utf8')->collation('utf8_unicode_ci');
            $table->foreign('regency_id')->references('id')->on('regencies');
            $table->char('district_id', 7)->charset('utf8')->collation('utf8_unicode_ci');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->char('village_id', 10)->charset('utf8')->collation('utf8_unicode_ci');
            $table->foreign('village_id')->references('id')->on('villages');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_offices');
    }
};
