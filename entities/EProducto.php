<?php
    class EProducto extends Entity{
        public $id;
        public $nombre;
        public $stock;
 
        public function __construct($input) {  $this->id=guid();$this->set($input);  }
    }
?>