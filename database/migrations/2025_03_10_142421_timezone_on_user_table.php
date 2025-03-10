<?php

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
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('offset');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->nullable()->constrained('timezones', 'name');
        });

        DB::table('timezones')->insert([
            ['name' => 'America/New_York', 'offset' => '-5'],
            ['name' => 'America/Chicago', 'offset' => '-6'],
            ['name' => 'America/Denver', 'offset' => '-7'],
            ['name' => 'America/Los_Angeles', 'offset' => '-8'],
            ['name' => 'America/Phoenix', 'offset' => '-7'],
            ['name' => 'America/Anchorage', 'offset' => '-9'],
            ['name' => 'America/Honolulu', 'offset' => '-10'],
            ['name' => 'America/San_Francisco', 'offset' => '-8'],
            ['name' => 'America/Seattle', 'offset' => '-8'],
            ['name' => 'America/Washington', 'offset' => '-5'],
            ['name' => 'America/Boston', 'offset' => '-5'],
            ['name' => 'America/Miami', 'offset' => '-5'],
            ['name' => 'America/New_Orleans', 'offset' => '-6'],
            ['name' => 'America/Atlanta', 'offset' => '-5'],
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });

        Schema::dropIfExists('timezones');
    }
};
