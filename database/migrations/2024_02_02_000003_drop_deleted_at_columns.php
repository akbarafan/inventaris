<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['barangs', 'kategoris', 'lokasis'] as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function ($table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['barangs', 'kategoris', 'lokasis'] as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function ($table) {
                    $table->softDeletes();
                });
            }
        }
    }
};