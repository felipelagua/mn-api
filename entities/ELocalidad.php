<?php
    class ELocalidad extends Entity{
        public $id;
        public $nombre;
        public $direccion;
 
        public function __construct($input) {  $this->set($input);  }
    }
?>