<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            try {
                if (Schema::hasTable('barangs')) {
                    Schema::table('barangs', function (Blueprint $table) {
                        try {
                            if (Schema::hasColumn('barangs', 'jumlah')) {
                                $table->unsignedInteger('jumlah')->default(0)->change();
                            }
                        } catch (Throwable $e) {
                        }
                        try {
                            if (Schema::hasColumn('barangs', 'baik')) {
                                $table->unsignedInteger('baik')->default(0)->change();
                            }
                        } catch (Throwable $e) {
                        }
                        try {
                            if (Schema::hasColumn('barangs', 'rusak')) {
                                $table->unsignedInteger('rusak')->default(0)->change();
                            }
                        } catch (Throwable $e) {
                        }
                        try {
                            if (Schema::hasColumn('barangs', 'rusak_berat')) {
                                $table->unsignedInteger('rusak_berat')->default(0)->change();
                            }
                        } catch (Throwable $e) {
                        }
                    });
                }
            } catch (Throwable $e) {
            }
        }

        foreach ([
            ['table' => 'kategoris', 'column' => 'nama_kategori'],
            ['table' => 'lokasis', 'column' => 'nama_lokasi'],
            ['table' => 'sumbers', 'column' => 'nama_sumber'],
        ] as $item) {
            try {
                if (! Schema::hasTable($item['table']) || ! Schema::hasColumn($item['table'], $item['column'])) {
                    continue;
                }
                if ($this->hasUnique($item['table'], $item['column'])) {
                    continue;
                }
                Schema::table($item['table'], function (Blueprint $table) use ($item) {
                    $table->unique($item['column']);
                });
            } catch (Throwable $e) {
            }
        }

        $indexes = [
            ['table' => 'barangs', 'column' => 'tanggal_masuk'],
            ['table' => 'activity_logs', 'column' => 'action'],
            ['table' => 'activity_logs', 'column' => 'created_at'],
            ['table' => 'scan_logs', 'column' => 'created_at'],
            ['table' => 'scan_logs', 'column' => 'kode_barang'],
        ];

        foreach ($indexes as $idx) {
            try {
                if (! Schema::hasTable($idx['table']) || ! Schema::hasColumn($idx['table'], $idx['column'])) {
                    continue;
                }
                if ($this->hasIndex($idx['table'], $idx['column'])) {
                    continue;
                }
                Schema::table($idx['table'], function (Blueprint $table) use ($idx) {
                    $table->index($idx['column']);
                });
            } catch (Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        foreach ([
            ['table' => 'kategoris', 'column' => 'nama_kategori'],
            ['table' => 'lokasis', 'column' => 'nama_lokasi'],
            ['table' => 'sumbers', 'column' => 'nama_sumber'],
        ] as $item) {
            try {
                Schema::table($item['table'], function (Blueprint $table) use ($item) {
                    $table->dropUnique([$item['column']]);
                });
            } catch (Throwable $e) {
            }
        }

        $indexes = [
            ['table' => 'barangs', 'column' => 'tanggal_masuk'],
            ['table' => 'activity_logs', 'column' => 'action'],
            ['table' => 'activity_logs', 'column' => 'created_at'],
            ['table' => 'scan_logs', 'column' => 'created_at'],
            ['table' => 'scan_logs', 'column' => 'kode_barang'],
        ];

        foreach ($indexes as $idx) {
            try {
                Schema::table($idx['table'], function (Blueprint $table) use ($idx) {
                    $table->dropIndex([$idx['column']]);
                });
            } catch (Throwable $e) {
            }
        }
    }

    private function hasUnique(string $table, string $column): bool
    {
        try {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $db = DB::getDatabaseName();
                $exists = DB::selectOne(
                    "SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = 'UNIQUE'",
                    [$db, $table]
                );
                $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Column_name = ? AND Non_unique = 0", [$column]);
                return ! empty($indexes);
            }
            if ($driver === 'sqlite') {
                $indexes = DB::select("SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name = ?", [$table]);
                foreach ($indexes as $idx) {
                    if (isset($idx->sql) && str_contains($idx->sql, $column) && str_contains(strtoupper($idx->sql), 'UNIQUE')) {
                        return true;
                    }
                }
            }
        } catch (Throwable $e) {
        }
        return false;
    }

    private function hasIndex(string $table, string $column): bool
    {
        try {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Column_name = ?", [$column]);
                return ! empty($indexes);
            }
            if ($driver === 'sqlite') {
                $indexes = DB::select("SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name = ?", [$table]);
                foreach ($indexes as $idx) {
                    if (isset($idx->sql) && str_contains($idx->sql, $column)) {
                        return true;
                    }
                }
                $tableInfo = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (! empty($tableInfo) && isset($tableInfo[0]->sql) && str_contains($tableInfo[0]->sql, $column)) {
                    return false;
                }
            }
        } catch (Throwable $e) {
        }
        return false;
    }
};
