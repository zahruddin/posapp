<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    //
    public function backup()
    {
        // Jalankan perintah backup
        // Buat nama file backup
        $filename = 'backup-' . date('Y-m-d_H-i-s') . '.sql';

        // Path simpan sementara
        $path = storage_path("app/{$filename}");

        // Ambil konfigurasi database dari .env
        $db = config('database.connections.mysql');
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            $db['username'],
            $db['password'],
            $db['host'],
            $db['database'],
            $path
        );

        // Jalankan command mysqldump
        $result = null;
        $output = null;
        exec($command, $output, $result);

        if ($result !== 0 || !file_exists($path)) {
            return back()->with('error', 'Gagal melakukan backup database.');
        }

        // Kirim file ke browser untuk di-download
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
