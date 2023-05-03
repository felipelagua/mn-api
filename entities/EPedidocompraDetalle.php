<?php
    class EPedidocompraDetalle extends Entity{
        public $id;
        public $localidadid;
        public $productoid;
        public $descripcion;
        public $cantidad;
 
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }  
            if(!isset($this->cantidad)){
                $this->cantidad=0;
            }   
        }
    }
?>