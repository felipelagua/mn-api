<?php
    function db_proveedor_buscar($nombre){
        $sql=" select id ,nombre
            from proveedor 
             where activo=1 
             and nombre like  '%".$nombre."%'
             order by nombre";
        return $sql;
    }
?>