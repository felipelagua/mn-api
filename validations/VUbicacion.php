<?php
    class VUbicacion extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->isGuid($o->localidadid,"localidadid");
            $this->required($o->nombre,"nombre");
            $this->process();
        }
    }
?>