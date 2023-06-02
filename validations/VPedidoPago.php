<?php
    class VPedidoPago extends Validator{
        
        function validate($o){
 
            $this->exists("cuenta",$o->cuentaid,true,"La cuentaid no es válido");
            $this->mayorque($o->pago,0.01,"El pago debe ser mayor a cero");
            $this->process();
        }
    }
?>