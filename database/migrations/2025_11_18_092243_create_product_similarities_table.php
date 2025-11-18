<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_similarities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('similar_product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('similarity_score', 5, 4)->comment('0.0000 to 1.0000');
            $table->string('similarity_type')->default('collaborative')->comment('collaborative, content_based, hybrid');
            $table->timestamp('computed_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['product_id', 'similar_product_id']);
            $table->index(['product_id', 'similarity_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_similarities');
    }
};
