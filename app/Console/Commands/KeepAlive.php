<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class KeepAlive extends Command
{
    protected $signature = 'app:keep-alive';
    protected $description = 'Mantiene la base de datos activa para evitar que el plan gratis de Render la duerma';

    public function handle()
    {
        try {
            DB::select('SELECT 1');
            $this->info('Keep-alive ping successful.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Keep-alive ping failed: ' . $e->getMessage());
            return 1;
        }
    }
}
