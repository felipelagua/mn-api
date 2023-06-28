<?php
    class EServicio extends Entity{
        public $id;
        public $proveedorid;
        public $productoid;     
        public $localidadid; 
        public $vence;
        public $duracionmes;
        public $fijo;
        public $monto;
       
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->id=guid();
                $this->proveedorid="";
                $this->productoid="";
                $this->localidadid="";
                $this->duracionmes=0;
                $this->vence="";
                $this->fijo="";
                $this->monto=0.00;
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>