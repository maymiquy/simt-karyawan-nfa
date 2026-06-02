<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('status');
        });

        // Extend status enum to include 'overdue'
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending','in_progress','completed','overdue','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert status enum (convert any 'overdue' rows back to 'pending' first)
        DB::statement("UPDATE tasks SET status = 'pending' WHERE status = 'overdue'");
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
