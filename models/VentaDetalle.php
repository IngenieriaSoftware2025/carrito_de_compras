<?php

namespace Model;

class VentaDetalle extends ActiveRecord {

    public static $tabla = 'venta_detalle';
    public static $columnasDB = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public static $idTabla = 'detalle_id';
    public $detalle_id;
    public $venta_id;
    public $producto_id;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;

    public function __construct($args = []){
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->venta_id = $args['venta_id'] ?? 0;
        $this->producto_id = $args['producto_id'] ?? 0;
        $this->cantidad = $args['cantidad'] ?? 0;
        $this->precio_unitario = $args['precio_unitario'] ?? 0.00;
        $this->subtotal = $args['subtotal'] ?? 0.00;
    }

}