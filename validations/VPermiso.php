<?php
    class VPermiso extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->codigo, "codigo");
            $this->required($o->nombre, "nombre");
            $this->process();
        }
    }
?>