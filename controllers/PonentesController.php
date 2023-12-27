<?php

namespace Controllers;

use Classes\Paginacion;
use Model\Ponente;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;

class PonentesController
{

    //LEER
    public static function index(Router $router)
    {
        if (!is_Admin()) {
            header('Location: /login');
        }
        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        if (!$pagina_actual || $pagina_actual < 1) {
            header('Location: /admin/ponentes?page=1');
        }
        $registros_por_pagina = 10;
        $total_registros = Ponente::total();
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total_registros);
        if ($paginacion->total_paginas() < $pagina_actual) {
            header('Location: /admin/ponentes?page=1');
        }
        $ponentes = Ponente::paginar($registros_por_pagina, $paginacion->offset());



        // Render a la vista 
        $router->render('admin/ponentes/index', [
            'titulo' => 'Ponentes / Conferencistas',
            'ponentes' => $ponentes,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    //CREAR
    public static function crear(Router $router)
    {
        $alertas = [];
        $ponente = new Ponente;
        if (!is_Admin()) {
            header('Location: /');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_Admin()) {
                header('Location: /');
            }
            //Leer imagen
            if (!empty($_FILES['imagen']['tmp_name'])) {
                $carpeta_imagenes = '../public/img/speakers';
                //crear la carpeta si no existe
                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0755, true);
                }
                //Generamos las versiones para la imagen
                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('webp', 80);

                $nombre_imagen = md5(uniqid(rand(), true));

                $_POST['imagen'] = $nombre_imagen;
            }
            //ponemos las redes sociales como un texto plano
            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);
            //sincronizamos el objeto ponente con la info del formulario
            $ponente->sincronizar($_POST);

            //Validamos el formulario
            $alertas = $ponente->validar();

            //Guardar el registro
            if (empty($alertas)) {


                //Guardar imagenes
                $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
                $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');

                //Guardar en la bd
                $resultado = $ponente->guardar();
                if ($resultado) {
                    header('Location: /admin/ponentes');
                }
            }
        }

        // Render a la vista 
        $router->render('admin/ponentes/crear', [
            'titulo' => 'Registrar Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes)
        ]);
    }

    //EDITAR
    public static function editar(Router $router)
    {
        $alertas = [];
        if (!is_Admin()) {
            header('Location: /login');
        }

        //validar ID
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location:/admin/ponentes');
        }
        //obtener ponente a editar
        $ponente = Ponente::find($id);
        if (!$ponente) {
            header('Location:/admin/ponentes');
        }
        $ponente->imagen_actual = $ponente->imagen;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_Admin()) {
                header('Location: /login');
            }
            //Leer imagen
            if (!empty($_FILES['imagen']['tmp_name'])) {
                $carpeta_imagenes = '../public/img/speakers';
                //crear la carpeta si no existe
                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0755, true);
                }
                //Generamos las versiones para la imagen
                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(800, 800)->encode('webp', 80);

                $nombre_imagen = md5(uniqid(rand(), true));

                $_POST['imagen'] = $nombre_imagen;
            } else {
                $_POST['imagen'] = $ponente->imagen_actual;
            }
            //ponemos las redes sociales como un texto plano
            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);

            $ponente->sincronizar($_POST);
            $alertas = $ponente->validar();

            if (empty($alertas)) {
                if (isset($nombre_imagen)) {
                    //Guardar imagenes
                    $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
                    $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');
                }
                $resultado = $ponente->guardar();
                if ($resultado) {
                    header('Location: /admin/ponentes');
                }
            }
        }

        // Render a la vista 
        $router->render('admin/ponentes/editar', [
            'titulo' => 'Editar Información del Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes)
        ]);
    }

    //ELIMINAR
    public static function eliminar()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_Admin()) {
                header('Location: /login');
            }
            $id = $_POST['id'];
            $ponente = Ponente::find($id);
            if (!isset($ponente)) {
                header('Location: /admin/ponentes');
            }
            $resultado = $ponente->eliminar();
            if ($resultado) {
                header('Location: /admin/ponentes');
            }
        }
    }
}
