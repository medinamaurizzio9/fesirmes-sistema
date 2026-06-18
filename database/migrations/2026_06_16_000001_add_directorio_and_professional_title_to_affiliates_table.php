<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->enum('professional_title', ['LIC.', 'DR.', 'DRA.'])->nullable()->after('status');
            $table->boolean('is_directorio')->default(false)->after('professional_title');
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn(['professional_title', 'is_directorio']);
        });
    }
};
