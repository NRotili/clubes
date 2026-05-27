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
        Schema::table('socios', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('observaciones');
            $table->uuid('qr_uuid')->unique()->nullable()->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('socios', function (Blueprint $table) {
            $table->dropColumn(['foto', 'qr_uuid']);
        });
    }
};
