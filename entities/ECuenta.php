<?php
    class ECuenta extends Entity{
        public $id;
        public $usuarioid;
        public $nombre;
        public $saldo_inicial;
        public function __construct($input) {  
            $this->id=guid();
            $this->set($input);  
        }
    }
?>