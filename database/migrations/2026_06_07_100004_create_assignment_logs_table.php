<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // aktor
            $table->enum('type', ['created', 'started', 'submitted', 'revised', 'approved']);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable(); // mis. {"activity_count":3,"kpi":8,"late":true}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_logs');
    }
};
