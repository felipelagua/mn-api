<?php
function db_notaingreso_producto_buscar($localidadid,$usuarioid,$nombre){
    $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
    from producto 
     where activo=1 and stock='SI'
     and not id in (select productoid from notaingresotemp_detalle
     where localidadid='$localidadid' and usuario_creacion='$usuarioid')
     and nombre like  '%$nombre%'
     order by nombre";
    return $sql;
}
function db_notaingreso_producto_buscar_nombre($localidadid,$usuarioid,$nombre){
    $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
    from producto 
     where activo=1 and stock='SI'
     and not id in (select productoid from notaingresotemp_detalle
     where localidadid='$localidadid' and usuario_creacion='$usuarioid')
     and nombre =  '$nombre'
     order by nombre";
    return $sql;
}
?>