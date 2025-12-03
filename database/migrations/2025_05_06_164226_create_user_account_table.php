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
        Schema::create('user_accounts', function (Blueprint $table) {
           $table->id();
        $table->char('user_id', 26)->unique();
            $table->string('username');
            $table->string('email_address')->unique();
            $table->string('dodo_customer_id', 255);
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->string('sign_up_type');
            $table->boolean('verify_status')->default(false);
            $table->string('verify_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
