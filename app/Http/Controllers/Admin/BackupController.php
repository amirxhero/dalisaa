<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupController extends Controller
{
    private function getDiskName(): string
    {
        $disks = config('backup.backup.destination.disks', ['local']);
        return reset($disks) ?: 'local';
    }

    private function getBackupDirectory(): string
    {
        return config('backup.backup.name', env('APP_NAME', 'laravel-backup'));
    }

    public function index()
    {
        $diskName  = $this->getDiskName();
        $disk      = Storage::disk($diskName);
        $directory = $this->getBackupDirectory();

        $files = $disk->exists($directory) ? $disk->files($directory) : [];

        $backups = collect($files)
            ->filter(fn ($file) => str_ends_with($file, '.zip'))
            ->map(function ($file) use ($disk) {
                return [
                    'path'          => $file,
                    'name'          => basename($file),
                    'size'          => $disk->size($file),
                    'last_modified' => $disk->lastModified($file),
                ];
            })
            ->sortByDesc('last_modified')
            ->values();

        return view('admin.backups.index', compact('backups', 'diskName'));
    }

    public function create(Request $request)
    {
        $option   = $request->input('option', 'only-db');
        $isDownload = $request->boolean('download', true) || $request->has('download');

        try {
            $diskName  = $this->getDiskName();
            $disk      = Storage::disk($diskName);
            $directory = $this->getBackupDirectory();

            $beforeFiles = $disk->exists($directory) ? $disk->files($directory) : [];

            // Attempt spatie backup artisan command first
            $exitCode = 1;
            try {
                if ($option === 'only-db') {
                    $exitCode = Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);
                } else {
                    $exitCode = Artisan::call('backup:run', ['--disable-notifications' => true]);
                }
            } catch (\Throwable $e) {
                $exitCode = 1;
            }

            $afterFiles = $disk->exists($directory) ? $disk->files($directory) : [];
            $hasNewFile = count($afterFiles) > count($beforeFiles);

            // Fallback zip generator if spatie backup artisan command failed (e.g. missing sqlite3 binary on local)
            if ($exitCode !== 0 || !$hasNewFile) {
                $createdFilePath = $this->createBackupZipDirectly($option === 'full');
            } else {
                $latestFile = collect($afterFiles)
                    ->filter(fn ($f) => str_ends_with($f, '.zip'))
                    ->sortByDesc(fn ($f) => $disk->lastModified($f))
                    ->first();
                $createdFilePath = $latestFile ? $disk->path($latestFile) : null;
            }

            if ($isDownload && $createdFilePath && file_exists($createdFilePath)) {
                return response()->download($createdFilePath, basename($createdFilePath), [
                    'Content-Type' => 'application/zip',
                ]);
            }

            return redirect()->route('admin.backups.index')
                ->with('success', 'پشتیبان‌گیری با موفقیت انجام شد.');
        } catch (\Throwable $e) {
            return back()->with('error', 'خطا در فرآیند پشتیبان‌گیری: ' . $e->getMessage());
        }
    }

    public function download(string $filename): BinaryFileResponse
    {
        $diskName  = $this->getDiskName();
        $disk      = Storage::disk($diskName);
        $directory = $this->getBackupDirectory();
        $filePath  = $directory . '/' . basename($filename);

        if (!$disk->exists($filePath)) {
            abort(404, 'فایل پشتیبان پیدا نشد.');
        }

        return response()->download($disk->path($filePath), basename($filePath), [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function destroy(string $filename)
    {
        $diskName  = $this->getDiskName();
        $disk      = Storage::disk($diskName);
        $directory = $this->getBackupDirectory();
        $filePath  = $directory . '/' . basename($filename);

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return back()->with('success', 'فایل بکاپ با موفقیت حذف شد.');
        }

        return back()->with('error', 'فایل یافت نشد.');
    }

    private function createBackupZipDirectly(bool $full = false): string
    {
        $diskName  = $this->getDiskName();
        $disk      = Storage::disk($diskName);
        $directory = $this->getBackupDirectory();

        $disk->makeDirectory($directory);

        $dateStr  = now()->format('Y-m-d-H-i-s');
        $filename = $directory . '/' . $dateStr . ($full ? '-full' : '-db') . '.zip';
        $fullZipPath = $disk->path($filename);

        // Ensure target directory exists on real filesystem
        $parentDir = dirname($fullZipPath);
        if (!is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($fullZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Backup SQLite Database file if present
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $zip->addFile($dbPath, 'db-dumps/sqlite-database.sqlite');
            }

            if ($full) {
                if (file_exists(base_path('.env'))) {
                    $zip->addFile(base_path('.env'), '.env');
                }
                if (file_exists(base_path('composer.json'))) {
                    $zip->addFile(base_path('composer.json'), 'composer.json');
                }
            }

            $zip->close();
        }

        return $fullZipPath;
    }
}
