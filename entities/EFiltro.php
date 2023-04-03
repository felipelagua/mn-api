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
        public function __construct($input) {  
            if($input!=null){
                $this->set($input); 
            }
             
        }
    }
?>