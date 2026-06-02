<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->text('completion_notes')->nullable()->after('progress');
            $table->text('manager_notes')->nullable()->after('completion_notes');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['completion_notes', 'manager_notes', 'reviewed_at']);
        });
    }
};
