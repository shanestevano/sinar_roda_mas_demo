<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemController extends Controller
{
    /**
     * View Laravel system logs (storage/logs/laravel.log)
     */
    public function logs(): View
    {
        $logPath = storage_path('logs/laravel.log');
        $logEntries = [];
        $fileSize = 0;

        if (File::exists($logPath)) {
            $fileSize = File::size($logPath);
            $content = File::get($logPath);

            // Pattern to match Laravel log header: [YYYY-MM-DD HH:MM:SS] environment.LEVEL: message
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] ([a-z0-9_]+)\.([A-Z]+): (.*?)(?=(\[\d{4}-\d{2}-\d{2}|$))/s';
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                // Reverse to get most recent logs first
                $reversed = array_reverse($matches);
                foreach (array_slice($reversed, 0, 100) as $match) {
                    $logEntries[] = [
                        'timestamp' => $match[1],
                        'env' => $match[2],
                        'level' => $match[3],
                        'message' => trim($match[4]),
                    ];
                }
            } else if (! empty($content)) {
                // Fallback line by line if format varies
                $lines = array_reverse(explode("\n", trim($content)));
                foreach (array_slice($lines, 0, 50) as $line) {
                    if (trim($line) !== '') {
                        $logEntries[] = [
                            'timestamp' => Carbon::now()->toDateTimeString(),
                            'env' => 'system',
                            'level' => 'INFO',
                            'message' => $line,
                        ];
                    }
                }
            }
        }

        return view('system.logs', compact('logEntries', 'fileSize'));
    }

    public function clearLogs(): RedirectResponse
    {
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        ActivityLog::record('CLEAR_LOGS', 'Membersihkan file catatan log sistem (storage/logs/laravel.log).');

        return redirect()->route('system.logs')->with('success', 'File catatan log sistem berhasil dibersihkan.');
    }

    /**
     * Database backup overview & download
     */
    public function backup(): View
    {
        $sqlitePath = database_path('database.sqlite');
        $hasSqlite = File::exists($sqlitePath);
        $sqliteSize = $hasSqlite ? File::size($sqlitePath) : 0;
        $dbConnection = config('database.default');

        return view('system.backup', compact('hasSqlite', 'sqliteSize', 'dbConnection'));
    }

    public function downloadBackup(): BinaryFileResponse|RedirectResponse
    {
        $sqlitePath = database_path('database.sqlite');
        if (File::exists($sqlitePath)) {
            ActivityLog::record('BACKUP_DOWNLOAD', 'Mengunduh cadangan database (database.sqlite).');
            $filename = 'sinar_roda_mas_backup_' . date('Y-m-d_His') . '.sqlite';

            return response()->download($sqlitePath, $filename);
        }

        return back()->with('error', 'File database SQLite tidak ditemukan untuk diunduh.');
    }
}
