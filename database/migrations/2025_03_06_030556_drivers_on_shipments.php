<?php

use App\Enums\ContactType;
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
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('contacts');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('contact_type')->default(ContactType::GENERAL->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('contact_type');
        });
    }
};
