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
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('btcpay');
            $table->string('invoice_id')->unique();
            $table->enum('status', ['pending', 'paid', 'confirmed', 'expired', 'underpaid', 'invalid', 'settlement_received'])->default('pending');
            $table->integer('amount_ore')->default(0);
            $table->string('btc_amount')->nullable();
            $table->string('rate')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_last_event')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};