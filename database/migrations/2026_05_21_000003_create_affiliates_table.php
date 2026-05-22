<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string('ci', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('joined_at')->nullable();
            $table->enum('status', ['activo', 'baja', 'suspendido', 'observado'])->default('activo')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
