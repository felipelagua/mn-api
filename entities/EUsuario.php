<?php
    class EUsuario extends Entity{
        public $id;
        public $usuario; 
        public $nombre;
 
        public function __construct($input) { 
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>