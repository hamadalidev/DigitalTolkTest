<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTestTranslations extends Command
{
    protected $signature = 'translations:generate {count=100000}';
    protected $description = 'Generate test translations';

    public function handle()
    {
        $count = $this->argument('count');
        $this->info("Generating {$count} translations...");

        // Create default locales if they don't exist
        $locales = ['en', 'fr', 'es', 'de', 'it'];
        foreach ($locales as $code) {
            Locale::firstOrCreate(
                ['code' => $code],
                ['name' => strtoupper($code)]
            );
        }

        // Get all locale IDs
        $localeIds = Locale::pluck('id')->toArray();

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Generate translations in chunks to avoid memory issues
        $chunkSize = 1000;
        for ($i = 0; $i < $count; $i += $chunkSize) {
            $currentChunk = min($chunkSize, $count - $i);
            
            // Create translations with existing locales
            Translation::factory()
                ->count($currentChunk)
                ->create([
                    'locale_id' => function() use ($localeIds) {
                        return $localeIds[array_rand($localeIds)];
                    }
                ]);
                
            $bar->advance($currentChunk);
        }

        $bar->finish();
        $this->newLine();
        $this->info('Translations generated successfully!');
    }
} 