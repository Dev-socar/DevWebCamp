<?php

namespace Controllers;

use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Paquete;
use Model\Ponente;
use Model\Usuario;
use Model\Registro;
use Model\Categoria;
use Model\EventosRegistros;
use Model\Regalo;

class RegistroController
{

    public static function crear(Router $router)
    {
        if (!is_Auth()) {
            header('Location: /');
            return;
        }

        //Verificar si el usuario ya esta registrado

        $registro = Registro::where('usuario_id', $_SESSION['id']);

        if (isset($registro) && $registro->paquete_id === "3" || isset($registro) && $registro->paquete_id === "2") {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }
        if(isset($registro) && $registro->paquete_id === "1"){
            header('Location: /finalizar-registro/conferencias');
            return;
        }
        // Render a la vista 
        $router->render('registro/crear', [
            'titulo' => 'Finalizar Registro'
        ]);
    }
    public static function gratis(Router $router)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_Auth()) {
                header('Location: /login');
                return;
            }

            if (isset($registro) && $registro->paquete_id === "3") {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }
            $token = substr(md5(uniqid(rand(), true)), 0, 8);
            //Crear Registro
            $datos = [
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            ];

            $registro = new Registro($datos);
            $resultado = $registro->guardar();
            if ($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }
        }
    }

    public static function boleto(Router $router)
    {

        //validar Url
        $id = $_GET['id'];
        if (!$id || !strlen($id) === 8) {
        }

        //buscarlo en la BD
        $registro = Registro::where('token', $id);
        if (!$registro) {
            header('Location: /');
            return;
        }

        $registro->usuario =  Usuario::find($registro->usuario_id);
        $registro->paquete =  Paquete::find($registro->paquete_id);



        // Render a la vista 
        $router->render('registro/boleto', [
            'titulo' => 'Asistencia a DevWebCamp',
            'registro' => $registro
        ]);
    }

    public static function pagar(Router $router)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_Auth()) {
                header('Location: /login');
                return;
            }

            //valida que post no venga vacio
            if (empty($_POST)) {
                echo json_encode([]);
                return;
            }
            //Crear registro

            $datos = $_POST;
            $datos['token'] = substr(md5(uniqid(rand(), true)), 0, 8);
            $datos['usuario_id'] = $_SESSION['id'];

            try {
                $registro = new Registro($datos);
                $resultado = $registro->guardar();
                echo json_encode($resultado);
            } catch (\Throwable $th) {
                echo json_encode([
                    'resultado' => 'Error'
                ]);
            }
        }
    }

    public static function conferencias(Router $router)
    {


        if (!is_Auth()) {
            header('Location: /login');
            return;
        }

        //Validar que tiene plan presencial

        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);

        if(isset($registro) && $registro->paquete_id === "2"){
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        if ($registro->paquete_id !== "1") {
            header('Location: /');
            return;
        }

        // //Redireccionar a boleto en caso de finalizar el registro
        // if(isset($registro->regalo_id) && $registro->paquete_id === "1"){
        //     header('Location: /boleto?id=' . urlencode($registro->token));
        //     return;
        // }

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = [];
        foreach ($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);

            if ($evento->dia_id === "1" & $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_l'][] = $evento;
            }
            if ($evento->dia_id === "2" & $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_m'][] = $evento;
            }
            if ($evento->dia_id === "1" & $evento->categoria_id === "2") {
                $eventos_formateados['workshops_l'][] = $evento;
            }
            if ($evento->dia_id === "2" & $evento->categoria_id === "2") {
                $eventos_formateados['workshops_m'][] = $evento;
            }
        }

        $regalos = Regalo::all('ASC');

        //Manejando el registro mediante $_POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //revisar si el usuario esta logueado
            if (!is_Auth()) {
                header('Location: /login');
                return;
            }

            $eventos = explode(',', $_POST['eventos']);
            if (empty($eventos)) {
                echo json_encode(['resultado' => false]);
                return;
            }

            //obtener el registro del usuario
            $usuarioRegistro = Registro::where('usuario_id', $_SESSION['id']);
            if (!isset($usuarioRegistro) || $usuarioRegistro->paquete_id !== "1") {
                echo json_encode(['resultado' => false]);
            }

            $eventos_array = [];

            //validar la disponibilidad  de los eventos seleccionados
            foreach ($eventos as $evento_id) {
                $evento = Evento::find($evento_id);
                //comprobar que el evento exista
                if (!isset($evento) || $evento->disponibles === "0") {
                    echo json_encode(['resultado' => false]);
                    return;
                }

                $eventos_array[] = $evento;
            }

            foreach ($eventos_array as $evento) {
                $evento->disponibles -= 1;
                $evento->guardar();

                //Almacenar el registro
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id
                ];
                $registroUsuario = new EventosRegistros($datos);
                $registroUsuario->guardar();
            }

            //Almacear el regalo
            $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);

            $resultado = $registro->guardar();
            if($resultado){
                echo json_encode(['resultado' => $resultado, 'token' => $registro->token]);
            }else{
                echo json_encode(['resultado' => false]);
            }
            return;
        }

        // Render a la vista 
        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops y Conferencias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos
        ]);
    }
}
