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
        'backup_file' => 'required|file|mimes:sql,txt|max:102400', // max 100MB
    ]);

    $path = $request->file('backup_file')->getRealPath();

    if (!is_readable($path)) {
        return back()->with('error', 'File SQL tidak dapat dibaca');
    }

    $sql = file_get_contents($path);

    if (trim($sql) === '') {
        return back()->with('error', 'File SQL kosong');
    }

    try {
        // Nonaktifkan FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Eksekusi mentah
        DB::unprepared($sql);

    } catch (\Throwable $e) {

        return back()->with(
            'error',
            'Restore gagal (sebagian data mungkin sudah berubah): ' . $e->getMessage()
        );

    } finally {

        // Pastikan FK selalu aktif lagi
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $ex) {}
    }

    return back()->with('success', 'Database berhasil direstore');
}

}
