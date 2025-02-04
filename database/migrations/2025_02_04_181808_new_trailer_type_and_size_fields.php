<?php

use App\Models\Shipments\TrailerSize;
use App\Models\Shipments\TrailerType;
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

        Schema::create('trailer_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->integer('trailer_size_id')->nullable();
            $table->foreign('trailer_size_id')->references('id')->on('trailer_sizes');
        });

        TrailerSize::create([
            'name' => '53'
        ]);

        TrailerSize::create([
            'name' => '48'
        ]);

        TrailerType::where('name', '53\' Dry Van')->update([
            'name' => 'Dry Van'
        ]);

        TrailerType::where('name', '53\' Reefer')->update([
            'name' => 'Reefer'
        ]);

        TrailerType::where('name', '53\' Flatbed')->update([
            'name' => 'Flatbed'
        ]);

        TrailerType::where('name', '53\' Stepdeck')->update([
            'name' => 'Stepdeck'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('trailer_size_id');
        });

        Schema::dropIfExists('trailer_sizes');


    }
};
