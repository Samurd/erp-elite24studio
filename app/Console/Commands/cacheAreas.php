<?php

namespace App\Console\Commands;

use App\Models\Area;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class cacheAreas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache-areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $areas = Area::with('parent')->get()->keyBy('slug');
        Cache::forever('area_structure', $areas);
        $this->info('✅ Estructura de áreas cargada en la caché.');
    }
}
