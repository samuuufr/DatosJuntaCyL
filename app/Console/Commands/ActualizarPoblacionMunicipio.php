<?php

namespace App\Console\Commands;

use App\Models\Municipio;
use Illuminate\Console\Command;

class ActualizarPoblacionMunicipio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poblacion:actualizar {codigo_ine} {poblacion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la población de un municipio por su código INE';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $codigoIne = $this->argument('codigo_ine');
        $poblacion = $this->argument('poblacion');

        $municipio = Municipio::where('codigo_ine', $codigoIne)->first();

        if (!$municipio) {
            $this->error("No se encontró ningún municipio con código INE: {$codigoIne}");
            return 1;
        }

        $municipio->poblacion = $poblacion;
        $municipio->save();

        $this->info("✓ Población actualizada para {$municipio->nombre}: " . number_format($poblacion, 0, ',', '.') . " habitantes");
        return 0;
    }
}
