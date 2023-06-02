<?php
    class VTipopedido extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre,"nombre");
            $this->required($o->clave,"clave");
            $this->process();
        }
    }
?>