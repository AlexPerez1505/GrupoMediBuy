<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * El espacio de nombres para los controladores.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Registrar cualquier servicio de la aplicación.
     */
    public function register()
    {
        //
    }

    /**
     * Definir los enlaces del modelo de ruta, filtros de patrón y otra configuración de rutas.
     */
    public function boot()
    {
        // Agregar lógica aquí si es necesario, si no, déjalo vacío
    }

    /**
     * Configurar las rutas de la aplicación.
     */
    public function map()
    {
        //
    }
}
