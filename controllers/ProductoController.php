<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Productos;
use MVC\Router;

class ProductoController extends ActiveRecord
{

    public function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        $_POST['producto_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['producto_nombre']))));
        $cantidad_nombres = strlen($_POST['producto_nombre']);

        if ($cantidad_nombres < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de dígitos que debe contener el nombre debe ser mayor a dos'
            ]);
            return;
        }

        $_POST['producto_descripcion'] = htmlspecialchars($_POST['producto_descripcion']);

        $_POST['producto_precio'] = filter_var($_POST['producto_precio'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if (!is_numeric($_POST['producto_precio']) || $_POST['producto_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser un número mayor a 0'
            ]);
            return;
        }

        $_POST['producto_cantidad'] = filter_var($_POST['producto_cantidad'], FILTER_SANITIZE_NUMBER_INT);

        if (!ctype_digit($_POST['producto_cantidad']) || $_POST['producto_cantidad'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número entero igual o mayor a 0'
            ]);
            return;
        }

        try {
            $data = new Productos([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_descripcion' => $_POST['producto_descripcion'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_cantidad' => $_POST['producto_cantidad'],
                'producto_situacion' => 1
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito, el producto ha sido registrado correctamente'
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

    public static function buscarAPI()
    {
        try {

            $sql = "SELECT * FROM productos WHERE producto_situacion = 1";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos',
                'detalle' => $e->getMessage(),
            ]);
        }
    }


    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['producto_id'];

        $_POST['producto_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['producto_nombre']))));
        $cantidad_nombres = strlen($_POST['producto_nombre']);

        if ($cantidad_nombres < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de dígitos que debe de contener el nombre debe ser mayor a dos'
            ]);
            return;
        }

        $_POST['producto_descripcion'] = htmlspecialchars($_POST['producto_descripcion']);

        $_POST['producto_precio'] = filter_var($_POST['producto_precio'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (!is_numeric($_POST['producto_precio']) || $_POST['producto_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser un número mayor a 0'
            ]);
            return;
        }

        $_POST['producto_cantidad'] = filter_var($_POST['producto_cantidad'], FILTER_SANITIZE_NUMBER_INT);
        if (!ctype_digit($_POST['producto_cantidad']) || $_POST['producto_cantidad'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número entero igual o mayor a 0'
            ]);
            return;
        }

        try {
            $data = Productos::find($id);

            $data->sincronizar([
                'producto_nombre' => $_POST['producto_nombre'],
                'producto_descripcion' => $_POST['producto_descripcion'],
                'producto_precio' => $_POST['producto_precio'],
                'producto_cantidad' => $_POST['producto_cantidad'],
                'producto_situacion' => 1
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del producto ha sido modificada exitosamente'
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

    public static function EliminarAPI()
    {
        try {

            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $ejecutar = Productos::EliminarProductos($id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El registro ha sido eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }


}