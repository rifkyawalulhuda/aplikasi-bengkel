<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_settings', function (Blueprint $table): void {
            $table->text('footer_address')->nullable();
            $table->decimal('footer_latitude', 10, 7)->nullable();
            $table->decimal('footer_longitude', 10, 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'footer_address',
                'footer_latitude',
                'footer_longitude',
            ]);
        });
    }
};
