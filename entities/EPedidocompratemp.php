<?php
    class EPedidocompratemp extends Entity{
        public $id;
        public $numero;
 
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->numero="";
            }
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>