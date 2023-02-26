<?php
    class ECuentaDetalle extends Entity{
        public $id;
        public $cuentaid;
        public $tipo;
        public $descripcion;
        public $monto;
        public function __construct() { 
            $this->id=guid();
        }
    }
?>