<?php
    class VLogin extends Validator{       
        function validate($o){
            $this->required($o->usuario,"El usuario es obligatorio");
            $this->required($o->clave,"La contraseña es obligatoria");
            $this->process();
        }
    }
?>