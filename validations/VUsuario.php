<?php
    class VUsuario extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->minLength($o->usuario,4,"usuario");
            $this->minLength($o->clave,3,"contraseña");
            $this->process();
        }
    }
?>