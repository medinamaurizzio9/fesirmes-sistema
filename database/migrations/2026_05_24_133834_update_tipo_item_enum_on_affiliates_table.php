<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE affiliates MODIFY tipo_item ENUM('SEDES', 'MINISTERIAL', 'INLASA', 'SEDEGES') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE affiliates SET tipo_item = NULL WHERE tipo_item IN ('INLASA', 'SEDEGES')");
        DB::statement("ALTER TABLE affiliates MODIFY tipo_item ENUM('SEDES', 'MINISTERIAL') NULL");
    }
};
