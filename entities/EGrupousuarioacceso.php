<?php
    class EGrupousuarioacceso extends Entity{
        public $id;
        public $grupousuarioid;
        public $permisoid;
        public $activado;
        public function __construct($input) { 
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>