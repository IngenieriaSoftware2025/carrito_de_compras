<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use Model\VentaDetalle;
use Model\Clientes;
use Model\Productos;
use MVC\Router;

class VentaController extends ActiveRecord
{

    public function renderizarPagina(Router $router)
    {
        // Obtener clientes para el select
        $sql_clientes = "SELECT cliente_id, cliente_nombres, cliente_apellidos, cliente_nit 
                        FROM clientes 
                        WHERE cliente_situacion = 1 
                        ORDER BY cliente_nombres";
        $clientes = self::fetchArray($sql_clientes);

        // Obtener productos para la tabla
        $sql_productos = "SELECT producto_id, producto_nombre, producto_descripcion, producto_precio, producto_cantidad 
                         FROM productos 
                         WHERE producto_situacion = 1 AND producto_cantidad > 0
                         ORDER BY producto_nombre";
        $productos = self::fetchArray($sql_productos);

        $router->render('ventas/index', [
            'clientes' => $clientes,
            'productos' => $productos
        ]);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        try {
            // Validar que se haya seleccionado un cliente
            $cliente_id = filter_var($_POST['cliente_id'], FILTER_SANITIZE_NUMBER_INT);
            if (!$cliente_id || $cliente_id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar un cliente'
                ]);
                return;
            }

            // Validar que existan productos en el carrito
            if (!isset($_POST['productos']) || empty($_POST['productos'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar al menos un producto'
                ]);
                return;
            }

            $productos = json_decode($_POST['productos'], true);
            if (empty($productos)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar al menos un producto'
                ]);
                return;
            }

            $total_venta = 0;
            $productos_validados = [];

            // Validar cada producto y calcular total
            foreach ($productos as $producto) {
                $producto_id = filter_var($producto['producto_id'], FILTER_SANITIZE_NUMBER_INT);
                $cantidad = filter_var($producto['cantidad'], FILTER_SANITIZE_NUMBER_INT);

                if ($cantidad <= 0) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Las cantidades deben ser mayores a 0'
                    ]);
                    return;
                }

                // Verificar que el producto existe y tiene stock
                $producto_db = Productos::find($producto_id);
                if (!$producto_db) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Producto no encontrado'
                    ]);
                    return;
                }

                if ($producto_db->producto_cantidad < $cantidad) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => "Stock insuficiente para {$producto_db->producto_nombre}. Disponible: {$producto_db->producto_cantidad}"
                    ]);
                    return;
                }

                $precio_unitario = $producto_db->producto_precio;
                $subtotal = $cantidad * $precio_unitario;
                $total_venta += $subtotal;

                $productos_validados[] = [
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio_unitario,
                    'subtotal' => $subtotal
                ];
            }

            // Crear la venta principal
            $venta = new Ventas([
                'cliente_id' => $cliente_id,
                'venta_fecha' => date('Y-m-d H:i:s'),
                'venta_total' => $total_venta,
                'venta_situacion' => 1
            ]);

            $resultado_venta = $venta->crear();

            if ($resultado_venta['resultado']) {
                $venta_id = $resultado_venta['id'];

                // Crear los detalles de la venta y actualizar stock
                foreach ($productos_validados as $detalle) {
                    // Crear detalle de venta
                    $venta_detalle = new VentaDetalle([
                        'venta_id' => $venta_id,
                        'producto_id' => $detalle['producto_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $detalle['subtotal']
                    ]);
                    $venta_detalle->crear();

                    // Actualizar stock del producto
                    $producto = Productos::find($detalle['producto_id']);
                    $nueva_cantidad = $producto->producto_cantidad - $detalle['cantidad'];
                    $producto->sincronizar(['producto_cantidad' => $nueva_cantidad]);
                    $producto->actualizar();
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta registrada exitosamente',
                    'venta_id' => $venta_id
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al crear la venta'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT 
                        v.venta_id,
                        v.venta_fecha,
                        v.venta_total,
                        v.venta_situacion,
                        c.cliente_nombres,
                        c.cliente_apellidos,
                        c.cliente_nit
                    FROM ventas v
                    INNER JOIN clientes c ON v.cliente_id = c.cliente_id
                    WHERE v.venta_situacion = 1
                    ORDER BY v.venta_fecha DESC";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las ventas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        try {
            $venta_id = filter_var($_POST['venta_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Validar que la venta existe
            $venta = Ventas::find($venta_id);
            if (!$venta) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            // Validar cliente
            $cliente_id = filter_var($_POST['cliente_id'], FILTER_SANITIZE_NUMBER_INT);
            if (!$cliente_id || $cliente_id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar un cliente'
                ]);
                return;
            }

            // Validar productos
            if (!isset($_POST['productos']) || empty($_POST['productos'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Debe seleccionar al menos un producto'
                ]);
                return;
            }

            $productos = json_decode($_POST['productos'], true);

            // 1. Restaurar stock de la venta anterior
            $sql_detalles_anteriores = "SELECT * FROM venta_detalle WHERE venta_id = $venta_id";
            $detalles_anteriores = self::fetchArray($sql_detalles_anteriores);
            
            foreach ($detalles_anteriores as $detalle) {
                $producto = Productos::find($detalle['producto_id']);
                if ($producto) {
                    $nueva_cantidad = $producto->producto_cantidad + $detalle['cantidad'];
                    $producto->sincronizar(['producto_cantidad' => $nueva_cantidad]);
                    $producto->actualizar();
                }
            }

            // 2. Eliminar detalles anteriores
            $sql_eliminar = "DELETE FROM venta_detalle WHERE venta_id = $venta_id";
            self::SQL($sql_eliminar);

            // 3. Validar nuevos productos y calcular total
            $total_venta = 0;
            $productos_validados = [];

            foreach ($productos as $producto) {
                $producto_id = filter_var($producto['producto_id'], FILTER_SANITIZE_NUMBER_INT);
                $cantidad = filter_var($producto['cantidad'], FILTER_SANITIZE_NUMBER_INT);

                if ($cantidad <= 0) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Las cantidades deben ser mayores a 0'
                    ]);
                    return;
                }

                $producto_db = Productos::find($producto_id);
                if (!$producto_db || $producto_db->producto_cantidad < $cantidad) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => "Stock insuficiente para {$producto_db->producto_nombre}"
                    ]);
                    return;
                }

                $precio_unitario = $producto_db->producto_precio;
                $subtotal = $cantidad * $precio_unitario;
                $total_venta += $subtotal;

                $productos_validados[] = [
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio_unitario,
                    'subtotal' => $subtotal
                ];
            }

            // 4. Actualizar venta principal
            $venta->sincronizar([
                'cliente_id' => $cliente_id,
                'venta_total' => $total_venta,
                'venta_situacion' => 1
            ]);
            $venta->actualizar();

            // 5. Crear nuevos detalles y actualizar stock
            foreach ($productos_validados as $detalle) {
                $venta_detalle = new VentaDetalle([
                    'venta_id' => $venta_id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal']
                ]);
                $venta_detalle->crear();

                // Descontar nuevo stock
                $producto = Productos::find($detalle['producto_id']);
                $nueva_cantidad = $producto->producto_cantidad - $detalle['cantidad'];
                $producto->sincronizar(['producto_cantidad' => $nueva_cantidad]);
                $producto->actualizar();
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta modificada exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function obtenerClientesAPI()
    {
        try {
            $sql = "SELECT cliente_id, cliente_nombres, cliente_apellidos, cliente_nit 
                    FROM clientes 
                    WHERE cliente_situacion = 1 
                    ORDER BY cliente_nombres";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function obtenerProductosAPI()
    {
        try {
            $sql = "SELECT producto_id, producto_nombre, producto_descripcion, producto_precio, producto_cantidad 
                    FROM productos 
                    WHERE producto_situacion = 1 AND producto_cantidad > 0
                    ORDER BY producto_nombre";
            
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
                'mensaje' => 'Error al obtener productos',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function validarStockAPI()
    {
        getHeadersApi();

        try {
            $producto_id = filter_var($_POST['producto_id'], FILTER_SANITIZE_NUMBER_INT);
            $cantidad = filter_var($_POST['cantidad'], FILTER_SANITIZE_NUMBER_INT);

            $producto = Productos::find($producto_id);
            
            if (!$producto) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Producto no encontrado'
                ]);
                return;
            }

            if ($producto->producto_cantidad < $cantidad) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Stock insuficiente. Disponible: {$producto->producto_cantidad}",
                    'stock_disponible' => $producto->producto_cantidad
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Stock disponible',
                'stock_disponible' => $producto->producto_cantidad
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al validar stock',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function obtenerDetalleVentaAPI()
    {
        try {
            $venta_id = filter_var($_GET['venta_id'], FILTER_SANITIZE_NUMBER_INT);

            $sql = "SELECT 
                        vd.*,
                        p.producto_nombre,
                        p.producto_descripcion
                    FROM venta_detalle vd
                    INNER JOIN productos p ON vd.producto_id = p.producto_id
                    WHERE vd.venta_id = $venta_id
                    ORDER BY vd.detalle_id";
            
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta obtenido correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el detalle',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}