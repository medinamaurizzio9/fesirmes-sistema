<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('qr_payload', 30);
            $table->unsignedInteger('qr_version')->default(1);
            $table->foreignId('regenerated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('regenerated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_credentials');
    }
};
