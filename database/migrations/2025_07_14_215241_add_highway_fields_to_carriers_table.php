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
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('highway_carrier_id')->nullable()->unique();
            $table->boolean('highway_monitored')->default(false);
            $table->timestamp('highway_last_synced_at')->nullable();
            $table->enum('highway_sync_status', ['not_synced', 'monitoring', 'sync_failed'])->default('not_synced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn([
                'highway_carrier_id',
                'highway_monitored', 
                'highway_last_synced_at',
                'highway_sync_status'
            ]);
        });
    }
};
