<?php
    class ETrasladotemp extends Entity{
        public $id;
        public $localidaddestinoid;
        public $solicitadoporid;
        public $pedidocompraid;
        public $comentario;
        public $pedidocompra_numero;

        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->localidaddestinoid="";
                $this->solicitadoporid="";
                $this->comentario="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>