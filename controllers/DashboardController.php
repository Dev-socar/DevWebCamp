<?php

namespace Controllers;

use Model\Evento;
use Model\Paquete;
use Model\Registro;
use Model\Usuario;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router)
    {

        
        //obtener ultimos registros
        $registros = Registro::get(5);
        foreach ($registros as $registro) {
            $registro->usuario = Usuario::find($registro->usuario_id);
            $registro->paquete = Paquete::find($registro->paquete_id);
        }
        
        //Calcular los ingresos totales
        $virtuales = Registro::total('paquete_id', 2);
        $presenciales = Registro::total('paquete_id', 1);
        $ingresos = ($virtuales * 46.41) + ($presenciales * 189.54);
        
        //Obtener Eventos con más y menos lugares

        $menosDisponibles = Evento::ordenarLimite('disponibles', 'ASC', 5);
        $masDisponibles = Evento::ordenarLimite('disponibles', 'DESC', 5);
        
        

        // Render a la vista 
        $router->render('admin/dashboard/index', [
            'titulo' => 'Panel de Administración',
            'registros' => $registros,
            'ingresos' => $ingresos,
            'menosDisponibles' => $menosDisponibles,
            'masDisponibles' => $masDisponibles
        ]);
    }
}
