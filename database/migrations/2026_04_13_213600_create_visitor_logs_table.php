<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table): void {
            $table->id();
            $table->date('visit_date');
            $table->string('ip_hash', 64);
            $table->string('session_key', 120)->nullable()->index();
            $table->string('path', 255);
            $table->string('referrer', 255)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_unique_daily')->default(false);
            $table->timestamps();

            $table->index(['visit_date', 'ip_hash']);
            $table->index(['visit_date', 'path']);
            $table->index(['visit_date', 'is_unique_daily']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
