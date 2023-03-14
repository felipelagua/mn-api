<?php
    class EProducto extends Entity{
        public $id;
        public $nombre;
        public $stock;
 
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }     
        }
    }
?>