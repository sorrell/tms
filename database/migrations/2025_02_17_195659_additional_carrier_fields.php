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
            $table->string('mc_number')->nullable();
            $table->string('dot_number')->nullable();
            
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('physical_location_id')->nullable();

            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_location_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn('mc_number');
            $table->dropColumn('dot_number');
            $table->dropColumn('contact_email');
            $table->dropColumn('contact_phone');
            $table->dropColumn('physical_location_id');

            $table->dropColumn('billing_email');
            $table->dropColumn('billing_phone');
            $table->dropColumn('billing_location_id');
        });
    }
};
