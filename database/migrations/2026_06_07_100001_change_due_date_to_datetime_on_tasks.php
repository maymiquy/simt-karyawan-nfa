<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah due_date dari DATE menjadi DATETIME agar KPI tepat-waktu presisi sampai jam.
        DB::statement('ALTER TABLE tasks MODIFY COLUMN due_date DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tasks MODIFY COLUMN due_date DATE NULL');
    }
};
