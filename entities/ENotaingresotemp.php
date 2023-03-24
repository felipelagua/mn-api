<?php
    class ENotaingresotemp extends Entity{
        public $id;
        public $motivoingresoid;
        public $comentario;

        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->motivoingresoid="";
                $this->comentario="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>