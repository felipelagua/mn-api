<?php
    class ECliente extends Entity{
        public $id;
        public $nombre;
        public $dni;
        public $celular;
        public $direccion;
        public $generico;
        public $ruc;
        public $domicilio_fiscal;
        public $credito;
        public function __construct($input) { 
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>