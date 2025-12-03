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
    Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->string('subscription_id')->unique();
     $table->char('user_id', 26);
        $table->foreign('user_id')->references('user_id')->on('user_accounts');
    $table->string('product_id')->nullable();
    $table->string('status')->nullable();
    $table->string('currency')->nullable();
    $table->decimal('amount', 15, 2)->nullable();
    $table->integer('payment_frequency_count')->nullable();
    $table->string('payment_frequency_interval')->nullable();
    $table->integer('subscription_period_count')->nullable();
    $table->string('subscription_period_interval')->nullable();
    $table->timestamp('next_billing_date')->nullable();
    $table->timestamp('previous_billing_date')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->json('raw_payload');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
