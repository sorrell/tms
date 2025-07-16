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
        Schema::create('highway_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->text('api_key'); // Will be encrypted
            $table->enum('environment', ['staging', 'production'])->default('staging');
            $table->boolean('auto_sync_enabled')->default(true);
            $table->string('sync_frequency')->default('daily');
            $table->timestamps();
            
            $table->unique('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highway_configurations');
    }
};
