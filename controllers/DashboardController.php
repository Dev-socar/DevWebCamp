<?php

namespace Controllers;

use Model\Paquete;
use Model\Ponente;
use Model\Registro;
use Model\Usuario;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router)
    {

        $ingresos = 0;
        //obtener ultimos registros
        $registros = Registro::get(5);
        foreach ($registros as $registro) {
            $registro->usuario = Usuario::find($registro->usuario_id);
            $registro->paquete = Paquete::find($registro->paquete_id);
            if ($registro->paquete_id === "1") {
                $ingresos += 199;
            } else {
                $ingresos += 149;
            }
        }
        $data = [

            'totalUsuarios' => Registro::total(),
            'totalPonentes' => Ponente::total(),
            'ingresos' => number_format($ingresos, 0, ',', ',')
        ];
        // debuguear($data);
        

        // Render a la vista 
        $router->render('admin/dashboard/index', [
            'titulo' => 'Panel de Administración',
            'registros' => $registros,
            'data' => $data
        ]);
    }
}
