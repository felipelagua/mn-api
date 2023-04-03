<?php
    class EUsuarioacceso extends Entity{
        public $id;
        public $usuarioid;
        public $localidadid;
        public $grupousuarioid;

        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
 
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>