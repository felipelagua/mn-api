<?php
    function db_tipocomprobante_listar(){
        $sql="SELECT id,nombre
        FROM tipocomprobante
        WHERE activo=1
        ORDER BY nombre";
        return $sql;
    }
?>