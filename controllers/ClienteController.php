<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Clientes;
use MVC\Router;

class ClienteController extends ActiveRecord
{

    public function renderizarPagina(Router $router)
    {
        $router->render('clientes/index', []);
    }

    public static function guardarAPI()
    {

        getHeadersApi();

        $_POST['cliente_nombres'] = ucwords(strtolower(trim(htmlspecialchars($_POST['cliente_nombres']))));

        $cantidad_nombres = strlen($_POST['cliente_nombres']);


        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['cliente_apellidos'] = ucwords(strtolower(trim(htmlspecialchars($_POST['cliente_apellidos']))));

        $cantidad_apellidos = strlen($_POST['cliente_apellidos']);

        if ($cantidad_apellidos < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el apellido debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['cliente_nit'] = filter_var($_POST['cliente_nit'], FILTER_SANITIZE_NUMBER_INT);

        $telefono_limpio = filter_var($_POST['cliente_telefono'], FILTER_SANITIZE_NUMBER_INT);
    
        if (strlen($telefono_limpio) != 8 || !filter_var($telefono_limpio, FILTER_VALIDATE_INT)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe contener exactamente 8 dígitos numéricos'
            ]);
            return;
        }
        $_POST['cliente_telefono'] = $telefono_limpio;

        
         $_POST['cliente_correo'] = filter_var($_POST['cliente_correo'], FILTER_SANITIZE_EMAIL);
    
        if (!filter_var($_POST['cliente_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ingresado es inválido'
            ]);
            return;
        }


            try {

                $data = new Clientes([
                    'cliente_nombres' => $_POST['cliente_nombres'],
                    'cliente_apellidos' => $_POST['cliente_apellidos'],
                    'cliente_nit' => $_POST['cliente_nit'],
                    'cliente_telefono' => $_POST['cliente_telefono'],
                    'cliente_correo' => $_POST['cliente_correo'],
                    'cliente_situacion' => 1
                ]);

                $crear = $data->crear();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Exito el cliente ha sido registrado correctamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar',
                    'detalle' => $e->getMessage(),
                ]);
            }
    }


}