<?php
    class ECuenta extends Entity{
        public $id;
        public $usuarioid;
        public $formapagoid;
        public $nombre;
        public $saldo_inicial;
        public $venta;
        public $cuentacierreid;
        public function __construct($input) {  
            $this->id=guid();
            $this->set($input);  
        }
    }
?>