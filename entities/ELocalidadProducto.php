<?php
    class ELocalidadProducto extends Entity{
        public $id;
        public $stock_minimo;
        public $stock_maximo;
 
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
                $this->stock_minimo=0;
                $this->stock_maximo=0;
            }     
        }
    }
?>