<?php
    class ELogin extends Entity{
        public $usuario;
        public $clave;
        public function __construct($input) {  
            if($input!=null){
                $this->set($input); 
            }
             
        }
    }
?>