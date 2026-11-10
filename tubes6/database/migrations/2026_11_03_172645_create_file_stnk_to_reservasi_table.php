<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            // Path relatif file di storage/app/public (contoh: stnk/abc123.jpg)
            $table->string('file_stnk')->nullable()->after('nomor_plat');
        });
    }

    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn('file_stnk');
        });
    }
};