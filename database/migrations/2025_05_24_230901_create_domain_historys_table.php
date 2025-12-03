<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('domain_histories', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->char('user_id', 26);
        $table->foreign('user_id')->references('user_id')->on('user_accounts');
        $table->char('domain_id', 36);
        $table->string('domain_name', 255);
        $table->decimal('renewed_price', 12, 2)->default(0);
        $table->integer('renewed_times')->default(0);
        $table->enum('status', ['available', 'owned', 'expired', 'sold'])->default('available');
        $table->string('lander_sold')->nullable();
        $table->decimal('sold_price', 12, 2)->nullable();
        $table->text('sale_note')->nullable();
        $table->string('sale_mode')->nullable();
        $table->string('acquisition_price')->nullable();
        $table->string('acquisition_method')->nullable();
        $table->decimal('revenue', 12, 2)->nullable();
        $table->string('registered_with')->nullable();
        $table->string('dns')->nullable();
        $table->dateTime('expires_at')->nullable();
        $table->decimal('total_acquisition_amount', 12, 2)->default(0);
        $table->timestamps();
        $table->enum('change_type', ['created', 'updated', 'deleted']);
        $table->timestamp('snapshot_at')->useCurrent();
        $table->string('keywords');
        $table->index('user_id');
        $table->index('domain_id');
        $table->string('description')->nullable();
        $table->index('status');
        $table->index('change_type');
        $table->string('date_sold')->nullable();
        $table->index('keywords');
        $table->index('snapshot_at');
        $table->softDeletes();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('domain_historys');
    }
};
