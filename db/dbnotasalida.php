
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
        INNER JOIN localidad_producto AS b ON b.productoid=a.productoid AND a.localidadid=b.localidadid
        where a.localidadid='$localidadid' 
        and a.usuario_creacion='$usuarioid'
        and a.activo=1
        and b.activo=1";
        return $sql;
    }
    function db_notasalida_producto_buscar($localidadid,$usuarioid,$nombre){
        $sql=" select a.id as productoid,a.nombre as descripcion,1 as cantidad, cast(ifnull(b.cantidad,0.00) as decimal(10,0))  as cantidad_stock
            from producto as a
            left join localidad_producto b on b.productoid=a.id and b.localidadid='$localidadid'  and b.activo=1
             where a.stock='SI' and a.activo=1
             and not a.id in (select productoid from notasalidatemp_detalle
             where localidadid='$localidadid' and usuario_creacion='$usuarioid')
             and a.nombre like  '%".$nombre."%'
             order by a.nombre";
            return $sql;
    }
    function db_notasalida_producto_buscar_nombre($localidadid,$usuarioid,$nombre){
        $sql=" select a.id as productoid,a.nombre as descripcion,1 as cantidad,cast(ifnull(b.cantidad,0.00) as decimal(10,0))  as cantidad_Stock
            from producto as a
            left join localidad_producto b on b.productoid=a.id and b.localidadid='$localidadid'  and b.activo=1
             where a.stock='SI' and a.activo=1
             and not a.id in (select productoid from notasalidatemp_detalle
             where localidadid='$localidadid' and usuario_creacion='$usuarioid')
             and a.nombre = '".$nombre."'
             order by a.nombre";
            return $sql;
    }
?>