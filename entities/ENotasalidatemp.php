<?php
    class ENotasalidatemp extends Entity{
        public $id;
        public $motivosalidaid;
        public $comentario;

        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->motivosalidaid="";
                $this->comentario="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>