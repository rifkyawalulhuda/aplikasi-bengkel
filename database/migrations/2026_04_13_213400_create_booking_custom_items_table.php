<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_custom_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('custom_service_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name_snapshot');
            $table->unsignedInteger('item_price_snapshot');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedInteger('subtotal');
            $table->timestamps();

            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_custom_items');
    }
};
