<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
// CRÍTICO: Importar el Middleware para poder registrarlo manualmente
use App\Http\Middleware\CheckUserRole;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    /**
     * Crea la aplicación. Este método es requerido por BaseTestCase.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // 1. Cargar el archivo de bootstrap de Laravel
        $app = require __DIR__ . '/../bootstrap/app.php';

        // 2. Ejecutar el bootstrap del Kernel de la consola.
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // 3. Cargar el Kernel HTTP (ya lo teníamos, lo dejamos para estabilidad)
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->bootstrap();

        // 4. SOLUCIÓN CRÍTICA: Registro manual del alias 'role'.
        // Esto garantiza que el alias esté disponible en el contenedor de servicios
        // del router de la aplicación de prueba, resolviendo el error de "Target class [role] does not exist".
        $app->make('router')->aliasMiddleware('role', CheckUserRole::class);


        return $app;
    }
}
