<?php
    class EMotivoma extends Entity{
        public $id;
        public $nombre;
        public $tipo;
        public function __construct($input) { 
            $this->id=guid();
            $this->set($input);
        }
    }
?>