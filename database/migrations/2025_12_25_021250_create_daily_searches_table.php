<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_searches', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('user_accounts')->onDelete('cascade');
            $table->date('date');
            $table->integer('count')->default(0);
            $table->timestamps();

            // Unique constraints
            $table->unique(['ip_address', 'date']);
            $table->unique(['user_id', 'date']);

            // Indexes for performance
            $table->index('ip_address');
            $table->index('user_id');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_searches');
    }
};
