<?php
    class EPedidotemp extends Entity{
        public $id;
        public $fecha_hora;
        public $localidadid;
        public $tipopedido;
        public $tipocomprobanteid;
        public $clienteid;
        public $cliente_nombre;
        public $tipocomprobante_nombre;
        public $numero;
        public $total;
        public $pago;
        public $saldo;
        public $tipopedido_nombre;
        public $direccion;
        public $ubicacionid;
        public $ubicacion_nombre;
        public $usuario_nombre;
        public $moneda="";
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->localidadid="";
                $this->tipocomprobanteid="";
                $this->clienteid="";
                $this->numero="";
                $this->cliente_nombre="";
                $this->moneda="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>