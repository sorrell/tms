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
        Schema::table('check_calls', function (Blueprint $table) {
            // Change reported_trailer_temp from dateTimeTz to string
            $table->string('reported_trailer_temp')->nullable()->change();
            
            // Change is_truck_empty from dateTimeTz to boolean
            $table->boolean('is_truck_empty')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_calls', function (Blueprint $table) {
            // Revert reported_trailer_temp back to dateTimeTz
            $table->dateTimeTz('reported_trailer_temp')->nullable()->change();
            
            // Revert is_truck_empty back to dateTimeTz
            $table->dateTimeTz('is_truck_empty')->nullable()->change();
        });
    }
};
