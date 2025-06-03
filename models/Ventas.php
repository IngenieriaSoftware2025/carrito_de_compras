<?php

namespace Model;

class Ventas extends ActiveRecord {

    public static $tabla = 'ventas';
    public static $columnasDB = [
        'cliente_id',
        'venta_fecha',
        'venta_total',
        'venta_situacion'
    ];

    public static $idTabla = 'venta_id';
    public $venta_id;
    public $cliente_id;
    public $venta_fecha;
    public $venta_total;
    public $venta_situacion;

    public function __construct($args = []){
        $this->venta_id = $args['venta_id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? 0;
        $this->venta_fecha = $args['venta_fecha'] ?? date('Y-m-d H:i:s');
        $this->venta_total = $args['venta_total'] ?? 0.00;
        $this->venta_situacion = $args['venta_situacion'] ?? 1;
    }

}