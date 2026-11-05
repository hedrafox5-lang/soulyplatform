<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminHostOpsController extends Controller
{
    /**
     * Render the Host Operations & Maintenance Command Center.
     */
    public function index()
    {
        $logFile = storage_path('logs/laravel.log');
        $logSize = File::exists($logFile) ? round(File::size($logFile) / 1024 / 1024, 2) : 0;
        
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $backups = collect(File::files($backupDir))->map(function ($file) {
            return [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024 / 1024, 2),
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        })->sortByDesc('date')->values();

        $serverInfo = [
            'php_version'    => PHP_VERSION,
            'laravel_version'=> app()->version(),
            'app_env'        => app()->environment(),
            'db_driver'      => DB::connection()->getDriverName(),
            'log_size_mb'    => $logSize,
        ];

        return view('admin.host-ops.index', compact('serverInfo', 'backups'));
    }

    /**
     * Execute safe, curated Artisan maintenance commands via web.
     */
    public function executeCommand(Request $request)
    {
        $validated = $request->validate([
            'command' => ['required', 'string'],
        ]);

        $allowedCommands = [
            'optimize:clear' => 'تنظيف كافة ملفات الكاش (Config, Routes, Views)',
            'config:cache'   => 'إعادة بناء كاش الإعدادات',
            'route:cache'    => 'إعادة بناء كاش المسارات',
            'view:cache'     => 'إعادة بناء كاش قوالب العرض',
            'migrate'        => 'تنفيذ الميجريشنز المعلقة (Database Migrate)',
            'storage:link'   => 'إعادة ربط مجلد التخزين بالملفات العامة (Storage Symlink)',
        ];

        $cmd = trim($validated['command']);

        if (!array_key_exists($cmd, $allowedCommands)) {
            return response()->json([
                'success' => false,
                'output'  => 'عفواً، هذا الأمر غير مسموح به حرصاً على استقرار السيرفر.',
            ], 403);
        }

        try {
            Artisan::call($cmd);
            $output = Artisan::output();

            Log::info("Admin HostOps executed command: {$cmd} by User: " . auth()->id());

            return response()->json([
                'success' => true,
                'output'  => !empty($output) ? $output : "تم تنفيذ الأمر [{$cmd}] بنجاح.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'output'  => 'خطأ أثناء التنفيذ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function runArtisanCommand(Request $request)
    {
        return $this->executeCommand($request);
    }

    /**
     * View recent application logs.
     */
    public function viewLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!File::exists($logFile)) {
            return response()->json(['success' => true, 'logs' => 'ملف السجلات فارغ حالياً.']);
        }

        // Read last 200 lines to avoid high memory usage on mobile
        $lines = array_slice(file($logFile), -200);
        $logContent = implode('', array_reverse($lines));

        return response()->json([
            'success' => true,
            'logs'    => !empty($logContent) ? $logContent : 'لا توجد أخطاء مسجلة.',
        ]);
    }

    /**
     * Clear application log file.
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return back()->with('citadel_toast', 'تم مسح وتفريغ ملف السجلات بنجاح.');
    }

    /**
     * Create an instant database SQL backup.
     */
    public function createDatabaseBackup()
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $tables = DB::select('SHOW TABLES');
            $dbNameKey = 'Tables_in_' . DB::getDatabaseName();
            
            $sqlDump = "-- Souly Platform SQL Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";

            foreach ($tables as $tableObj) {
                $tableName = $tableObj->$dbNameKey;
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0]->{'Create Table'};
                $sqlDump .= "\n\nDROP TABLE IF EXISTS `{$tableName}`;\n" . $createTable . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        return is_null($val) ? 'NULL' : "'" . addslashes((string)$val) . "'";
                    }, (array)$row);

                    $sqlDump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
            }

            $fileName = 'backup_' . date('Y_m_d_His') . '.sql';
            File::put($backupDir . '/' . $fileName, $sqlDump);

            return back()->with('citadel_toast', "تم أخذ نسخة احتياطية للقاعدة بنجاح: {$fileName}");

        } catch (\Throwable $e) {
            Log::error('DB Backup Failed: ' . $e->getMessage());
            return back()->with('error', 'فشل أخذ النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function backupDatabase()
    {
        return $this->createDatabaseBackup();
    }

    /**
     * Download a specific backup file.
     */
    public function downloadBackup(string $fileName): BinaryFileResponse
    {
        $filePath = storage_path('app/backups/' . basename($fileName));

        if (!File::exists($filePath)) {
            abort(404, 'ملف النسخة الاحتياطية غير موجود.');
        }

        return response()->download($filePath);
    }
}