<?php
    class EFiltro extends Entity{
        public $id;
        public $nombre;
        public $tipo;
        public $anio;
        public $mes;
        public $desde;
        public $hasta;
        public $motivo;
        public $usuariocreador;
        public $numero;
        public $localidaddestinoid ;
        public $localidadid ;
        public $solicitadoporid;
        public $activado;
        public $terminado;
        public $instantaneo;
        public $venta;
        public $movhoy;
        public $stock;
        public $otro;
        public $turno;
        public $productoid;
        public function __construct($input) {  
            if($input!=null){
                $this->set($input); 
            }
             
        }
    }
?>