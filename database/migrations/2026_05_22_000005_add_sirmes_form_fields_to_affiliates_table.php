<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->string('nombres', 150)->nullable()->after('ci');
            $table->string('apellido_paterno', 100)->nullable()->after('nombres');
            $table->string('apellido_materno', 100)->nullable()->after('apellido_paterno');
            $table->string('lugar_fecha_nacimiento')->nullable()->after('apellido_materno');
            $table->string('nacionalidad', 100)->nullable()->after('lugar_fecha_nacimiento');
            $table->string('domicilio')->nullable()->after('nacionalidad');
            $table->string('celular', 30)->nullable()->after('domicilio');
            $table->string('telefono', 30)->nullable()->after('email');
            $table->boolean('idioma_castellano')->default(false)->after('telefono');
            $table->boolean('idioma_ingles')->default(false)->after('idioma_castellano');
            $table->boolean('idioma_aymara')->default(false)->after('idioma_ingles');
            $table->boolean('idioma_quechua')->default(false)->after('idioma_aymara');
            $table->string('idioma_otros')->nullable()->after('idioma_quechua');
            $table->json('formacion_academica')->nullable()->after('idioma_otros');
            $table->string('diplomado_universidad')->nullable()->after('formacion_academica');
            $table->string('diplomado_anio', 10)->nullable()->after('diplomado_universidad');
            $table->string('diplomado_titulo')->nullable()->after('diplomado_anio');
            $table->string('especialidad_universidad')->nullable()->after('diplomado_titulo');
            $table->string('especialidad_anio', 10)->nullable()->after('especialidad_universidad');
            $table->string('especialidad_titulo')->nullable()->after('especialidad_anio');
            $table->string('maestria_universidad')->nullable()->after('especialidad_titulo');
            $table->string('maestria_anio', 10)->nullable()->after('maestria_universidad');
            $table->string('maestria_titulo')->nullable()->after('maestria_anio');
            $table->string('lugar_trabajo')->nullable()->after('maestria_titulo');
            $table->string('red_salud')->nullable()->after('lugar_trabajo');
            $table->string('item_principal')->nullable()->unique()->after('red_salud');
            $table->string('item_secundario')->nullable()->after('item_principal');
            $table->enum('tipo_item', ['SEDES', 'MINISTERIAL'])->nullable()->after('item_secundario');
            $table->date('fecha_ingreso_sistema')->nullable()->after('tipo_item');
            $table->date('fecha_primer_descuento_fesirmes')->nullable()->after('fecha_ingreso_sistema');
            $table->text('tematica_capacitacion')->nullable()->after('fecha_primer_descuento_fesirmes');
            $table->text('deportes')->nullable()->after('tematica_capacitacion');
            $table->string('photo_path')->nullable()->after('deportes');
        });

        DB::table('affiliates')->update([
            'nombres' => DB::raw('first_name'),
            'apellido_paterno' => DB::raw('last_name'),
            'domicilio' => DB::raw('address'),
            'celular' => DB::raw('phone'),
            'fecha_ingreso_sistema' => DB::raw('joined_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropUnique(['item_principal']);
            $table->dropColumn([
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'lugar_fecha_nacimiento',
                'nacionalidad',
                'domicilio',
                'celular',
                'telefono',
                'idioma_castellano',
                'idioma_ingles',
                'idioma_aymara',
                'idioma_quechua',
                'idioma_otros',
                'formacion_academica',
                'diplomado_universidad',
                'diplomado_anio',
                'diplomado_titulo',
                'especialidad_universidad',
                'especialidad_anio',
                'especialidad_titulo',
                'maestria_universidad',
                'maestria_anio',
                'maestria_titulo',
                'lugar_trabajo',
                'red_salud',
                'item_principal',
                'item_secundario',
                'tipo_item',
                'fecha_ingreso_sistema',
                'fecha_primer_descuento_fesirmes',
                'tematica_capacitacion',
                'deportes',
                'photo_path',
            ]);
        });
    }
};
