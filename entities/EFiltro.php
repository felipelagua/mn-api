<?php
    class EFiltro extends Entity{
        public $nombre;
        public function __construct($input) {  
            if($input!=null){
                $this->set($input); 
            }
             
        }
    }
?>