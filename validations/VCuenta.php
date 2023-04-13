<?php
    class VCuenta extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->exists("usuario",$o->usuarioid,true,"Usuario no válido");
            $this->required($o->nombre,"nombre");
            $this->yesno($o->venta,"El campo Venta no es válido");
            $this->process();
        }
    }
?>