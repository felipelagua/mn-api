<?php
    class EProducto extends Entity{
        public $id;
        public $nombre;
        public $clasificacion1id;
        public $clasificacion2id;
        public $clasificacion3id;
        public $stock;
        public $compra;
        public $venta;
        public $instantaneo;
        public $terminado;
 
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }     
        }
    }
?>