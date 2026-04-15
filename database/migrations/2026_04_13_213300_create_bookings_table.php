<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('booking_code')->unique();
            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 30);
            $table->string('motorcycle_type');
            $table->string('motorcycle_brand', 100);
            $table->string('motorcycle_model', 100);
            $table->string('motorcycle_year', 4)->nullable();
            $table->string('plate_number', 20)->nullable();
            $table->string('package_type');
            $table->foreignId('service_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('package_name_snapshot');
            $table->unsignedInteger('package_price_snapshot')->default(0);
            $table->text('notes')->nullable();
            $table->date('service_date');
            $table->string('service_time', 5);
            $table->string('status');
            $table->unsignedInteger('subtotal_price');
            $table->unsignedInteger('service_fee')->default(0);
            $table->unsignedInteger('total_price');
            $table->text('address_text');
            $table->string('house_landmark', 255);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('admin_notes')->nullable();
            $table->boolean('requires_manual_review')->default(false)->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['service_date', 'service_time', 'status']);
            $table->index(['status', 'service_date']);
            $table->index(['customer_name', 'customer_phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
