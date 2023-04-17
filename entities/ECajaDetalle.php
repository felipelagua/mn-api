<?php
    class ECajaDetalle extends Entity{
        public $id;
        public $cajaid;
        public $tipo;
        public $descripcion;
        public $monto;
        public $saldo;
        public function __construct($input) { 
            if($input!=null){
                $this->set($input); 
            }
            if($this->id==null){
                $this->id=guid();
            }
        }
    }
?>