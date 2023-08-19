<?php
    class EFormapago extends Entity{
        public $id;
        public $nombre;
        public $caja;
        public $imagen;
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->nombre="";
                $this->imagen="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>