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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->unsignedInteger('discount_value');
            $table->unsignedInteger('max_discount')->nullable()->comment('Batas maks potongan untuk tipe persentase');
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->unsignedInteger('max_usage')->nullable()->comment('Null = unlimited');
            $table->unsignedInteger('usage_count')->default(0);
            $table->boolean('is_active')->default(false);
            $table->enum('activation_mode', ['manual', 'auto'])->default('manual');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'valid_from', 'valid_until']);
            $table->index('activation_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
