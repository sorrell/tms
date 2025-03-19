<?php

use App\Models\Shipments\TrailerType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // create table trailer_types
        Schema::create('trailer_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->timestampsTz();
            $table->softDeletesTz();
        });


        Schema::table('shipments', function (Blueprint $table) {
            $table->float('weight')->nullable();
            $table->float('trip_distance')->nullable();

            // This weirdness is required for sqlite migrations to properly allow nullable foreign keys
            $table->unsignedBigInteger('trailer_type_id')->nullable();
            $table->foreign('trailer_type_id')->references('id')->on('trailer_types');

            $table->boolean('trailer_temperature_range')->default(false);
            $table->float('trailer_temperature')->nullable();
            $table->float('trailer_temperature_maximum')->nullable();
        });

        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->text('special_instructions')->nullable();
            $table->text('reference_numbers')->nullable();
            $table->integer('stop_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['trailer_type_id']);
            $table->dropColumn('trailer_type_id');
            $table->dropColumn('weight');
            $table->dropColumn('trip_distance');
            $table->dropColumn('trailer_temperature_range');
            $table->dropColumn('trailer_temperature');
            $table->dropColumn('trailer_temperature_maximum');
        });

        Schema::dropIfExists('trailer_types');

        Schema::table('shipment_stops', function (Blueprint $table) {
            $table->dropColumn('special_instructions');
            $table->dropColumn('reference_numbers');
            $table->dropColumn('stop_number');
        });
    }
};
