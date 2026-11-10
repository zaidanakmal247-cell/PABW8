<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->decimal('denda_nominal', 12, 2)->nullable();
            $table->text('denda_keterangan')->nullable();
        });

        DB::table('users')->where('role', 'admin')->update(['role' => 'petugas']);
        DB::table('users')
            ->where('role', 'petugas')
            ->where('email', '!=', 'petugas@parktrack.test')
            ->update(['role' => 'user']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'user')->update(['role' => 'petugas']);
        DB::table('users')->where('email', 'admin@parktrack.test')->update(['role' => 'admin']);

        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'denda_nominal', 'denda_keterangan']);
        });
    }
};
