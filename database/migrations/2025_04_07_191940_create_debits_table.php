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
        Schema::create('debits', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('currency');
            $table->date('tran_date');
            $table->string('sub_title')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();

            $table->unsignedBigInteger('billing_id')->nullable();
            $table->foreign('billing_id')->references('id')->on('addresses')->nullOnDelete();

            $table->unsignedBigInteger('business_id')->nullable();

            $table->decimal('sub_total', 20, 2)->nullable();
            $table->decimal('total_discount', 20, 2)->nullable();
            $table->decimal('amount_used', 20, 2)->nullable();
            $table->decimal('amount_unused', 20, 2)->nullable();

            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new');
            $table->enum('payment_status', ['paid', 'pending', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debits');
    }
};
