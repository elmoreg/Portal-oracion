<?php

namespace App\Console\Commands;

use App\Services\Assignment\AssignmentService;
use Illuminate\Console\Command;

class AssignPrayerRequests extends Command
{
    /**
     * @var string
     */
    protected $signature = 'prayers:assign
        {--dry-run : Muestra el plan de asignación sin persistir ningún cambio}';

    /**
     * @var string
     */
    protected $description = 'Asigna peticiones de oración pendientes a intercesores activos según zona y carga';

    public function handle(AssignmentService $service): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('Modo simulación (dry-run): no se persistirán cambios.');
        }

        $this->info('Iniciando asignación de peticiones de oración...');

        $result = $service->assign(dryRun: $isDryRun);

        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Peticiones asignadas', $result->assigned],
                ['Peticiones sin asignar (sin intercesor disponible)', $result->unassignable],
                ['Peticiones omitidas', $result->skipped],
                ['Total procesadas', $result->total()],
            ]
        );

        if ($result->hasUnassignable()) {
            $this->warn("Hay {$result->unassignable} petición(es) sin asignar por falta de intercesores disponibles.");
        }

        if ($isDryRun) {
            $this->warn('Simulación completada. Sin cambios en la base de datos.');
        } else {
            $this->info('Asignación completada.');
        }

        return self::SUCCESS;
    }
}
