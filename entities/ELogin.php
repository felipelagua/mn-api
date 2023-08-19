<?php
    class ELogin extends Entity{
        public $usuario;
        public $clave;
        public $nuevaclave;
        public $confirmaclave;
        public function __construct($input) {  
            if($input!=null){
                $this->set($input); 
            }
             
        }
    }
?>