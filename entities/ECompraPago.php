<?php
    class ECompraPago extends Entity{
        public $id;
        public $localidadid;
        public $cuentaid;
        public $descripcion;
        public $pago;
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }  
            if(!isset($this->pago)){
                $this->pago=0;
            }   
        }
    }
?>