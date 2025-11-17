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
        Schema::create('sku_unit', function (Blueprint $table) {
            $table->foreignId('sku_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_per_unit')->default(1);
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->primary(['sku_id', 'unit_id']);
            $table->index(['sku_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sku_unit');
    }
};