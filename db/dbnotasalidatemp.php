
<?php
    function db_notasalidatemp_obtener($localidadid,$usuarioid){
        $sql=" select id,motivosalidaid,comentario
             from notasalidatemp 
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";
        return $sql;
    }
    function db_notasalidatemp_detalle_listar($localidadid,$usuarioid){
        $sql =" SELECT a.id,a.productoid,a.descripcion,a.cantidad,b.cantidad AS cantidad_stock
        from notasalidatemp_detalle AS a
        INNER JOIN localidad_producto AS b ON b.productoid=a.productoid AND a.localidadid
        where a.localidadid='$localidadid' 
        and a.usuario_creacion='$usuarioid'
        and a.activo=1
        and b.activo=1";
        return $sql;
    }
?>