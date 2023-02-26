<?php
    class EUsuario extends Entity{
        public $id;
        public $usuario;
        public $clave;
        public $nombre;
 
        public function __construct($input) {  $this->set($input);  }
    }
?>