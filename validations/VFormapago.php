<?php
    class VFormapago extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre,"nombre");
            $this->yesno($o->caja,"El campo caja no es válido");
            $this->process();
        }
    }
?>