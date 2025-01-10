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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner_id')->constrained('users');
            $table->timestampsTz();
        });

        Schema::create('organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestampsTz();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_organization_id')->nullable()->constrained('organizations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_organization_id']);
            $table->dropColumn('current_organization_id');
        });
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organizations');
    }
};
