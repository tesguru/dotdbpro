<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
     Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->string('payment_id')->unique();
   $table->char('user_id', 26);
        $table->foreign('user_id')->references('user_id')->on('user_accounts');
    $table->string('subscription_id')->nullable();
    $table->string('business_id')->nullable();
    $table->string('status')->nullable();
    $table->decimal('total_amount', 15, 2)->nullable();
    $table->string('currency')->nullable();
    $table->string('payment_method')->nullable();
    $table->string('card_last_four')->nullable();
    $table->string('card_type')->nullable();
    $table->string('card_network')->nullable();
    $table->string('customer_id')->nullable();
    $table->string('customer_name')->nullable();
    $table->string('customer_email')->nullable();
    $table->json('raw_payload'); // store everything
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
