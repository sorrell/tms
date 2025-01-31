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

        Schema::dropIfExists('shipment_stop_appointments');

        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->dateTimeTz('appointment_at');
            $table->dateTimeTz('appointment_end_at')->nullable();
            $table->string('appointment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->dropColumn('appointment_at');
            $table->dropColumn('appointment_end_at');
            $table->dropColumn('appointment_type');
        });

        Schema::create('shipment_stop_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_stop_id')->constrained('shipment_stops');
            $table->dateTimeTz('appointment_at');
            $table->dateTimeTz('appointment_end_at')->nullable();
            $table->string('appointment_type')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });
    }
};

