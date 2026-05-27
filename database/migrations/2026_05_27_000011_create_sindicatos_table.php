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
        Schema::create('sindicatos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('sigla', 50)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->index();
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('sindicatos')->insert([
            'nombre' => Sindicato::DIRECT_NAME,
            'sigla' => 'FESIRMES',
            'estado' => 'activo',
            'observaciones' => 'Registro por defecto para afiliados directos de FESIRMES.',
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sindicatos');
    }
};
