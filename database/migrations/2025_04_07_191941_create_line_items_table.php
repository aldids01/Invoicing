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
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('receipt_id')->nullable()->constrained('receipts')->cascadeOnDelete();
            $table->foreignId('proforma_id')->nullable()->constrained('proformas')->cascadeOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained('estimates')->cascadeOnDelete();
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->cascadeOnDelete();
            $table->foreignId('credit_id')->nullable()->constrained('credits')->cascadeOnDelete();
            $table->foreignId('debit_id')->nullable()->constrained('debits')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('bills')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->integer('quantity')->nullable()->default(1);
            $table->integer('rate')->nullable()->default(0);
            $table->integer('tax')->nullable()->default(0);
            $table->integer('discount')->nullable()->default(0);
            $table->decimal('sub_total', 20, 2)->nullable()->default(0);
            $table->decimal('total_price', 20, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
