<?php
    class VUsuario extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre, "nombre");
            $this->minLength($o->usuario,4,"usuario"); 
            $this->process();
        }
    }
?>