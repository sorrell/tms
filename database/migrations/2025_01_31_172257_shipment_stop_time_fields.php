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
        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->dateTimeTz('eta')->nullable();
            $table->dateTimeTz('arrived_at')->nullable();
            $table->dateTimeTz('loaded_unloaded_at')->nullable();
            $table->dateTimeTz('left_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->dropColumn('eta');
            $table->dropColumn('arrived_at');
            $table->dropColumn('loaded_unloaded_at');
            $table->dropColumn('left_at');
        });
    }
};
