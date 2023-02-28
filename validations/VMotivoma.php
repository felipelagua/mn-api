<?php
    class VMotivoma extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre,"nombre");
            $this->typeOperation($o->tipo);
            $this->process();
        }
    }
?>