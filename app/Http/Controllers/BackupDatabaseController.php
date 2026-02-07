<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BackupDatabaseController extends Controller
{
    public function index()
    {
        return view('admin.database.backup-restore');
    }

    /**
     * BACKUP DATABASE (PURE PHP, NO EXEC)
     */
    public function download()
    {

        $dbName = DB::getDatabaseName();
        $filename = $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';

        $excludeTables = ['users', 'roles'];

        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . $dbName;

        $sql = "-- Backup Database: {$dbName}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            if (in_array($tableName, $excludeTables)) {
                continue;
            }

            // Struktur tabel
            $create = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};
            $sql .= "-- Table structure for {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sql .= $create . ";\n\n";

            // Data tabel
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return DB::getPdo()->quote($value);
                }, (array) $row);

                $sql .= "INSERT INTO `$tableName` VALUES (" . implode(',', $values) . ");\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return Response::make($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * RESTORE DATABASE
     */
    public function restore(Request $request)
    {


        $request->validate([
            'backup_file' => 'required|mimes:sql'
        ]);

        $sql = file_get_contents($request->file('backup_file')->getRealPath());

        // Hapus komentar
        $sql = preg_replace('/--.*?\n/', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        $queries = array_filter(array_map('trim', explode(";\n", $sql)));

        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $count = 0;
            foreach ($queries as $query) {
                DB::statement($query);
                $count++;
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Restore gagal: ' . $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', "Database berhasil direstore ({$count} query dijalankan)");
    }
}
