<?php
    class VCuentaTransferir extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->exists("cuenta",$o->cuentaorigenid,true,"Cuenta origen no válida");
            $this->numberValue($o->monto,"No es monto válido");
            $this->exists("cuenta",$o->cuentadestinoid,true,"Cuenta destino no válida");
            $this->process();
        }
    }
?>