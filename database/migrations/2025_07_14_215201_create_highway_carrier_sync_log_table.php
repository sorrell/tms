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
        Schema::create('highway_carrier_sync_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained()->onDelete('cascade');
            $table->enum('sync_type', ['add_to_monitoring', 'profile_sync']);
            $table->string('highway_carrier_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'retry']);
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            
            $table->index(['organization_id', 'carrier_id']);
            $table->index('status');
            $table->index('sync_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highway_carrier_sync_log');
    }
};
