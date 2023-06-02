<?php
    class EPedidoPago extends Entity{
        public $id;
        public $pedidoid;
        public $localidadid;
        public $cuentaid;
        public $descripcion;
        public $pago;
        public $monto;
        public $vuelto;
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }  
            if(!isset($this->pago)){
                $this->pago=0;
            } 
            if(!isset($this->monto)){
                $this->monto=0;
            } 
            if(!isset($this->vuelto)){
                $this->vuelto=0;
            }   
        }
    }
?>