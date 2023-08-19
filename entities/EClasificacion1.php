<?php
    class EClasificacion1 extends Entity{
        public $id;
        public $nombre;
        public $otro;
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->nombre="";
                $this->otro="NO";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>