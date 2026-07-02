<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ShowImageLinks extends Command
{
    protected $signature = 'images:links';
    protected $description = 'Show all image links from storage/app/public/categories (including subfolders)';

    public function handle()
    {
        $path = storage_path('app/public/categories');
        $this->info("Checking folder: " . $path);

        if (!is_dir($path)) {
            $this->error("Folder does not exist!");
            return;
        }

        $files = scandir($path);

        if ($files === false || count($files) <= 2) {
            $this->info("No files found in: " . $path);
            return;
        }

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $url = asset('storage/categories/' . $file);
                $this->line($url);
            }
        }
    }

}
