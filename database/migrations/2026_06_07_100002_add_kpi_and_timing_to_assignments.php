<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable()->after('assigned_by');
            $table->timestamp('started_at')->nullable()->after('submitted_at');
            $table->text('communication_note')->nullable()->after('manager_notes');
            $table->unsignedInteger('revision_count')->default(0)->after('communication_note');
            $table->decimal('kpi_score', 4, 1)->nullable()->after('revision_count');
        });

        // Tambah status 'submitted' (menunggu review) ke enum progress.
        DB::statement("ALTER TABLE assignments MODIFY COLUMN progress ENUM('not_started','on_progress','submitted','done','revision') NOT NULL DEFAULT 'not_started'");

        // Isi assigned_at untuk data lama berdasarkan created_at.
        DB::statement('UPDATE assignments SET assigned_at = created_at WHERE assigned_at IS NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE assignments MODIFY COLUMN progress ENUM('not_started','on_progress','done','revision') NOT NULL DEFAULT 'not_started'");

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['assigned_at', 'started_at', 'communication_note', 'revision_count', 'kpi_score']);
        });
    }
};
