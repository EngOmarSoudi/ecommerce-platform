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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->onDelete('cascade');
            $table->foreignId('carrier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->enum('calculation_method', ['flat', 'weight', 'quantity'])->default('flat');
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->decimal('rate_per_kg', 10, 2)->nullable()->comment('Rate per kilogram');
            $table->decimal('rate_per_unit', 10, 2)->nullable()->comment('Rate per item');
            $table->decimal('min_weight', 10, 2)->nullable();
            $table->decimal('max_weight', 10, 2)->nullable();
            $table->json('min_dimensions')->nullable()->comment('Min L x W x H in cm');
            $table->json('max_dimensions')->nullable()->comment('Max L x W x H in cm');
            $table->integer('estimated_days_min')->nullable();
            $table->integer('estimated_days_max')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['shipping_zone_id', 'is_active']);
            $table->index('carrier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
