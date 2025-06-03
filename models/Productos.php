<?php

namespace Model;

class Productos extends ActiveRecord {

    public static $tabla = 'productos';
    public static $columnasDB = [
        'producto_nombre',
        'producto_descripcion',
        'producto_precio',
        'producto_cantidad',
        'producto_situacion'
    ];

    public static $idTabla = 'producto_id';
    public $producto_id;
    public $producto_nombre;
    public $producto_descripcion;
    public $producto_precio;
    public $producto_cantidad;
    public $producto_situacion;

    public function __construct($args = []){
        $this->producto_id = $args['producto_id'] ?? null;
        $this->producto_nombre = $args['producto_nombre'] ?? '';
        $this->producto_descripcion = $args['producto_descripcion'] ?? '';
        $this->producto_precio = $args['producto_precio'] ?? 0;
        $this->producto_cantidad = $args['producto_cantidad'] ?? 0;
        $this->producto_situacion = $args['producto_situacion'] ?? 1;
    }

    public static function EliminarProductos($id)
{
    $producto = self::find($id);

    if ($producto && $producto -> producto_cantidad == 0) {
        return $producto->eliminar();
    }

    return false;
}



}