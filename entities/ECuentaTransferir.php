<?php
    class ECuentaTransferir extends Entity{
        public $id;
        public $cuentaorigenid;
        public $monto;
        public $cuentadestinoid;
        public function __construct() {  
            $this->id=guid(); 
            $this->set(input());
        }
    }
?>