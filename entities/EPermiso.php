<?php
    class EPermiso extends Entity{
        public $id;
        public $codigo;
        public $nombre;
 
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }     
        }
    }
?>