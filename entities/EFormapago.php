<?php
    class EFormapago extends Entity{
        public $id;
        public $nombre;
        public $caja;
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->nombre="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>