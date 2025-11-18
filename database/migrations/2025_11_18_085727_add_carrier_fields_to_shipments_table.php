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
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('carrier_id')->nullable()->after('order_id')->constrained()->onDelete('set null');
            $table->string('awb_number')->nullable()->after('tracking_number')->comment('Air Waybill Number');
            $table->decimal('weight', 10, 2)->nullable()->after('carrier_id');
            $table->string('tracking_url')->nullable()->after('awb_number');
            $table->string('label_url')->nullable()->after('tracking_url');
            
            $table->index('awb_number');
            $table->index('carrier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['carrier_id']);
            $table->dropIndex(['awb_number']);
            $table->dropIndex(['carrier_id']);
            $table->dropColumn(['carrier_id', 'awb_number', 'weight', 'tracking_url', 'label_url']);
        });
    }
};
