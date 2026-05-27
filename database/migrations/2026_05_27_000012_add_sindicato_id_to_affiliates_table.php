<?php

use App\Models\Sindicato;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('affiliates', 'sindicato_id')) {
            Schema::table('affiliates', function (Blueprint $table) {
                $table->foreignId('sindicato_id')
                    ->nullable()
                    ->after('tipo_item')
                    ->constrained('sindicatos')
                    ->nullOnDelete();
            });
        }

        $directSindicatoId = DB::table('sindicatos')
            ->where('nombre', Sindicato::DIRECT_NAME)
            ->value('id');

        if ($directSindicatoId) {
            DB::table('affiliates')
                ->whereNull('sindicato_id')
                ->update(['sindicato_id' => $directSindicatoId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('affiliates', 'sindicato_id')) {
            Schema::table('affiliates', function (Blueprint $table) {
                $table->dropConstrainedForeignId('sindicato_id');
            });
        }
    }
};
