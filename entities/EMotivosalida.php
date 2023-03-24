<?php
    class EMotivosalida extends Entity{
        public $id;
        public $nombre;
        public function __construct($input) { 
            $this->set($input);
            if(!isset($this->id)){
                $this->id=guid();
            } 
        }
    }
?>