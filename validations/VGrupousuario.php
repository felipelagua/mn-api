<?php
    class VGrupousuario extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre,"nombre");
            $this->process();
        }
    }
?>