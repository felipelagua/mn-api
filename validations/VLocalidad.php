<?php
    class VLocalidad extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre, "nombre");
            $this->required($o->direccion, "direccion");
            $this->process();
        }
    }
?>