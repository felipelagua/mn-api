<?php
    class Etrabajador extends Entity{
        public $id;
        public $nombre;
        public $dni;
        public $localidadid;
        public $sueldo;
       
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->nombre="";
                $this->dni="";
                $this->localidadid="";
                $this->sueldo=0.00;
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>