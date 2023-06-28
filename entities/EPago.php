<?php
    class EPago extends Entity{
        public $id;
        public $trabajadorid;
        public $anio;
        public $mes;
        public $cuentaid;
       
        public function __construct($input) {  
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            }     
        }
    }
?>