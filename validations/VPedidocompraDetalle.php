<?php
    class VPedidocompraDetalle extends Validator{
        
        function validate($o){
 
            $this->exists("producto",$o->productoid,true,"El productoid no es válido");
            $this->required($o->descripcion,"descripcion");
            $this->mayorque($o->cantidad,0,"La cantidad debe ser mayor o igual a 0");
            $this->process();
        }
    }
?>