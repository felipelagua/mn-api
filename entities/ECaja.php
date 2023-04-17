<?php
    class ECaja extends Entity{
        public $id;
        public $usuarioid;
        public $localidadid; 
        public $saldo_inicial;
        public $saldo;
        public $estado;
        public function __construct($input) {  
            $this->id=guid();
            $this->set($input);  
        }
    }
?>