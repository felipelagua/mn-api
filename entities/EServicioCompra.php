<?php
    class EServicioCompra extends Entity{
        public $id;
        public $servicioid;
        public $cuentaid;  
        public $anio;
        public $mes;
        public $tipocomprobanteid;
        public $numero;
        public $monto;
       
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->id=guid();
                $this->servicioid="";
                $this->cuentaid;  
                $this->anio;
                $this->mes;
                $this->tipocomprobanteid;
                $this->numero;
                $this->monto;
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>