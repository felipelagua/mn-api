<?php
    class VNotasalidaDetalle extends Validator{
        
        function validate($o){
 
            $this->exists("producto",$o->productoid,true,"El productoid no es válido");
            $this->required($o->descripcion,"descripcion");
            $this->mayorque($o->cantidad,1,"La cantidad debe ser mayor o igual a 1");
            $this->process();
        }
    }
?>