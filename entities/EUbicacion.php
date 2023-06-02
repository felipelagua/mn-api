<?php
    class EUbicacion extends Entity{
        public $id;
        public $nombre;
        public $localidadid;
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