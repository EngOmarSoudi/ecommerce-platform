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
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable()->index();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('event_type', ['view', 'cart_add', 'purchase', 'wishlist_add', 'review'])->index();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->json('metadata')->nullable()->comment('Additional event data');
            $table->timestamp('event_at')->useCurrent()->index();
            $table->timestamps();
            
            $table->index(['user_id', 'event_type', 'event_at']);
            $table->index(['product_id', 'event_type']);
            $table->index(['session_id', 'event_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
};
