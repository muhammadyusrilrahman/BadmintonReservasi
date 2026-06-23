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
        Schema::create('maintenance_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // system, court
            $table->foreignId('court_id')->nullable()->constrained('courts')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->date('scheduled_date');
            $table->string('duration');
            $table->string('target_type'); // all, affected
            $table->integer('recipients_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_broadcasts');
    }
};
