<?php
    class EProductoDestino extends Entity{
        public $id;
        public $productoid;
        public $itemid;
        public $cantidad;
 
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }  
            if(!isset($this->cantidad)){
                $this->cantidad=0;
            }   
        }
    }
?>