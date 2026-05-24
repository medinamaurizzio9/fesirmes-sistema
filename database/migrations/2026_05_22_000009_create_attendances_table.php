<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->restrictOnDelete();
            $table->foreignId('affiliate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ci_detectado', 50)->index();
            $table->enum('estado', ['valido', 'duplicado', 'observado', 'invalido'])->index();
            $table->text('observacion')->nullable();
            $table->foreignId('imported_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('imported_at')->useCurrent();
            $table->string('source_file_name')->nullable();
            $table->string('import_batch_id', 60)->index();
            $table->foreignId('reverted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reverted_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['activity_id', 'affiliate_id', 'estado', 'reverted_at'], 'attendance_unique_valid_affiliate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
