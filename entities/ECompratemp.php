<?php
    class ECompratemp extends Entity{
        public $id;
        public $localidadid;
        public $tipocomprobanteid;
        public $proveedorid;
        public $proveedor_nombre;
        public $tipocomprobante_nombre;
        public $numero;
        public $total;
        public $pago;
        public $saldo;

        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->localidadid="";
                $this->tipocomprobanteid="";
                $this->proveedorid="";
                $this->numero="";
                $this->proveedor_nombre="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>