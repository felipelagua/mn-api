<?php
    class EServicioEnt extends Entity{
        public $id;
        public $proveedorid;
        public $proveedor_nombre;
        public $productoid;
        public $producto_nombre;      
        public $localidadid;
        public $localidad_nombre;
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
                $this->proveedor_nombre="";
                $this->producto_nombre="";
                $this->localidad_nombre="";
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