<?php
    class EMotivosalida extends Entity{
        public $id;
        public $nombre;
        public function __construct($input) { 
            $this->id=guid();
            $this->set($input);
        }
    }
?>