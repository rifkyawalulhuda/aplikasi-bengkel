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
        Schema::table('booking_settings', function (Blueprint $table) {
            $table->decimal('transport_free_radius_km', 6, 2)->default(10)->after('service_fee');
            $table->unsignedInteger('transport_fee_per_km')->default(5000)->after('transport_free_radius_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_settings', function (Blueprint $table) {
            $table->dropColumn(['transport_free_radius_km', 'transport_fee_per_km']);
        });
    }
};
