<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('public_token')->unique();
            $table->string('number')->unique();
            $table->enum('status', ['pending_payment', 'paid', 'processing', 'shipped', 'completed', 'expired', 'cancelled', 'refunded', 'underpaid'])->default('pending_payment');
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_company')->nullable();
            $table->string('customer_org_number')->nullable();
            $table->string('billing_name')->nullable();
            $table->text('billing_line1')->nullable();
            $table->text('billing_line2')->nullable();
            $table->string('billing_postcode')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_country', 2)->nullable();
            $table->string('shipping_name')->nullable();
            $table->text('shipping_line1')->nullable();
            $table->text('shipping_line2')->nullable();
            $table->string('shipping_postcode')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_country', 2)->nullable();
            $table->integer('subtotal_ore')->default(0);
            $table->integer('discount_ore')->default(0);
            $table->integer('shipping_ore')->default(0);
            $table->integer('vat_ore')->default(0);
            $table->integer('total_ore')->default(0);
            $table->decimal('vat_rate', 5)->default(0.25);
            $table->string('locale', 5)->default('nb');
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->text('notes_internal')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'placed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};