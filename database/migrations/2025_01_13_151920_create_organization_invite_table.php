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
        Schema::create('organization_invites', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->foreignId('organization_id')->constrained();
            $table->string('email');
            $table->string('code');
            $table->timestampTz('accepted_at')->nullable();
            $table->timestampTz('expire_at');
            $table->foreignId('accepted_by_id')->nullable()->constrained('users');
            $table->softDeletesTz();

            $table->unique(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_invites');
    }
};
