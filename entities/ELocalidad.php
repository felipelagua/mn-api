<?php
    class ELocalidad extends Entity{
        public $id;
        public $nombre;
        public $direccion;
        public $venta;
        public $impresora;
        public $nombrecorto;

        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }     
        }
    }
?>