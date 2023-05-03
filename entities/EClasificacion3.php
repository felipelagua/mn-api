<?php
    class EClasificacion3 extends Entity{
        public $id;
        public $nombre;
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