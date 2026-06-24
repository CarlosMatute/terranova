<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AppSetup extends Command
{
    protected $signature = 'app:setup {--with-data : Cargar datos de prueba también}';
    protected $description = 'Ejecuta el schema SQL de Terranova y opcionalmente los datos de prueba';

    public function handle()
    {
        $this->info('Ejecutando schema de base de datos...');

        $schemaPath = storage_path('lotificadora_scripts_postgres.sql');
        if (!file_exists($schemaPath)) {
            $this->error("No se encontró el archivo: $schemaPath");
            return 1;
        }

        $sql = file_get_contents($schemaPath);
        DB::unprepared($sql);

        $this->info('Schema ejecutado correctamente.');

        if ($this->option('with-data')) {
            $this->info('Cargando datos de prueba...');
            $dataPath = storage_path('test_data.sql');
            if (!file_exists($dataPath)) {
                $this->error("No se encontró el archivo: $dataPath");
                return 1;
            }
            $dataSql = file_get_contents($dataPath);
            DB::unprepared($dataSql);
            $this->info('Datos de prueba cargados correctamente.');
        }

        return 0;
    }
}
