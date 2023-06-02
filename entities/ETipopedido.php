<?php
    class ETipopedido extends Entity{
        public $id;
        public $nombre;
        public $clave;
        public $ubicacion;
        public $delivery;
        public function __construct($input) { 
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>