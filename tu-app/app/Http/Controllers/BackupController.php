<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected string $backupPath = 'backups';

    /**
     * Display backup management page
     */
    public function index(): View
    {
        $backups = collect(Storage::files($this->backupPath))
            ->map(function ($path) {
                return [
                    'filename' => basename($path),
                    'path' => $path,
                    'size' => Storage::size($path),
                    'created_at' => Storage::lastModified($path),
                ];
            })
            ->sortByDesc('created_at')
            ->values();

        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Create a new backup
     */
    public function create(): RedirectResponse
    {
        try {
            $filename = 'backup_' . now()->format('Y-m-d_His') . '.sql';

            // Get database configuration
            $database = config('database.connections.pgsql.database');
            $username = config('database.connections.pgsql.username');
            $password = config('database.connections.pgsql.password');
            $host = config('database.connections.pgsql.host');
            $port = config('database.connections.pgsql.port');

            // Create backup directory if not exists
            if (!Storage::exists($this->backupPath)) {
                Storage::makeDirectory($this->backupPath);
            }

            $outputPath = storage_path("app/{$this->backupPath}/{$filename}");

            // Execute pg_dump command
            $command = sprintf(
                'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s 2>&1',
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($database),
                escapeshellarg($outputPath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                return back()->withErrors(['backup' => 'Backup gagal. Error: ' . implode("\n", $output)]);
            }

            // Log the backup action
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup',
                'model_type' => 'Database',
                'model_id' => 0,
                'new_values' => ['filename' => $filename],
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup berhasil dibuat: {$filename}");

        } catch (\Exception $e) {
            return back()->withErrors(['backup' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Download a backup file
     */
    public function download(string $filename)
    {
        $path = "{$this->backupPath}/{$filename}";

        if (!Storage::exists($path)) {
            return back()->withErrors(['backup' => 'File backup tidak ditemukan.']);
        }

        return Storage::download($path, $filename);
    }
}
