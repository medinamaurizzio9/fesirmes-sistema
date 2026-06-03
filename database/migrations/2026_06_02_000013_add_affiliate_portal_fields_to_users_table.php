<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('affiliate_id')->nullable()->after('role')->constrained('affiliates')->nullOnDelete();
            $table->boolean('must_change_password')->default(false)->after('affiliate_id');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            $table->boolean('is_blocked')->default(false)->after('password_changed_at');
            $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            $table->foreignId('blocked_by')->nullable()->after('blocked_at')->constrained('users')->nullOnDelete();
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('Administrador', 'Secretaría', 'Consulta', 'Afiliado') NOT NULL DEFAULT 'Consulta'");
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'Afiliado')->update(['role' => 'Consulta']);
        DB::statement("ALTER TABLE users MODIFY role ENUM('Administrador', 'Secretaría', 'Consulta') NOT NULL DEFAULT 'Consulta'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('blocked_by');
            $table->dropColumn([
                'is_blocked',
                'blocked_at',
                'password_changed_at',
                'must_change_password',
            ]);
            $table->dropConstrainedForeignId('affiliate_id');
        });
    }
};

