<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use function Livewire\store;

class CleanupStorageFiles extends Command
{
    protected $signature = 'storage:cleanup';
    protected $description = 'Clean up old files from storage directory';

    public function handle()
    {
        $directories = [
            '/documents/loan',
        ];

        $totalCleaned = 0;

        foreach ($directories as $directory) {
            if (Storage::disk('public')->exists($directory)) {
                // Ambil semua file dalam direktori
                $files = Storage::disk('public')->files($directory);

                $this->info("Files found in {$directory}:");
                $this->info(print_r($files, true));

                foreach ($files as $file) {
                    try {
                        // Tambahkan log sebelum penghapusan
                        $this->info("Attempting to delete: {$file}");

                        if (Storage::disk('public')->exists($file)) {
                            Storage::disk('public')->delete($file);
                            $totalCleaned++;
                            $this->info("Successfully deleted: {$file}");
                        } else {
                            $this->warn("File not found: {$file}");
                        }
                    } catch (\Exception $e) {
                        $this->error("Error processing {$file}: " . $e->getMessage());
                    }
                }
            } else {
                $this->warn("Directory not found: {$directory}");

                // Tampilkan daftar direktori yang ada untuk debugging
                $this->info("Available directories:");
                $this->info(print_r(Storage::allDirectories('public'), true));
            }
        }

        $this->info("Storage cleanup completed. Total files deleted: {$totalCleaned}");
    }
}
