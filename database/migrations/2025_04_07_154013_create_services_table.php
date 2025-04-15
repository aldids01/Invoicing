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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sac')->unique()->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('qty')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('weight_value', 10, 2)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('weight_unit')->default('kg');
            $table->decimal('height_value', 10, 2)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('height_unit')->default('cm');
            $table->decimal('width_value', 10, 2)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('width_unit')->default('cm');
            $table->decimal('depth_value', 10, 2)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('depth_unit')->default('cm');
            $table->decimal('volume_value', 10, 2)->nullable()
                ->default(0.00)
                ->unsigned();
            $table->string('volume_unit')->default('l');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
