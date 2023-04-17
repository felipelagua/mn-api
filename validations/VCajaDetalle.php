<?php
    class VCajaDetalle extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->exists("cuenta",$o->cuentaid,true,"Cuenta no válida");
            $this->required($o->descripcion,"descripcion");
            $this->typeOperation($o->tipo);
            $this->numberValue($o->monto,"No es monto válido");
            $this->process();
        }
    }
?>