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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('currency');
            $table->date('tran_date');
            $table->string('sub_title')->nullable();
            $table->string('shipping_method')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();

            $table->unsignedBigInteger('billing_id')->nullable();
            $table->foreign('billing_id')->references('id')->on('addresses')->nullOnDelete();

            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('addresses')->nullOnDelete();

            $table->unsignedBigInteger('business_id')->nullable();

            $table->decimal('sub_total', 20, 2)->nullable();
            $table->decimal('total_discount', 20, 2)->nullable();
            $table->decimal('shipping_cost', 20, 2)->nullable();
            $table->decimal('total', 20, 2)->nullable();

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
        Schema::dropIfExists('estimates');
    }
};
