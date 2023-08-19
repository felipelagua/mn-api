<?php
function db_produccion_producto_buscar($localidadid,$usuarioid,$nombre){
    $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
    from producto 
     where activo=1 
     and terminado='SI'
     and not id in (select productoid from producciontemp_detalle
     where localidadid='$localidadid' and usuario_creacion='$usuarioid') 
     and nombre like  '%$nombre%'
     order by fecha_hora_creacion desc";
    return $sql;
}
function db_produccion_producto_buscar_nombre($localidadid,$usuarioid,$nombre){
    $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
    from producto 
     where activo=1 
     and terminado='SI'
     and not id in (select productoid from producciontemp_detalle
     where localidadid='$localidadid' and usuario_creacion='$usuarioid') 
     and busquedamicro = '$nombre'
     order by fecha_hora_creacion desc";
    return $sql;
}
function db_producciontemp_actualizar($localidadid,$usuarioid,$comentario){
    $sql="update producciontemp set comentario='$comentario' where localidadid='$localidadid' and usuario_creacion='$usuarioid'";
    return $sql;
}
function db_producciontemp_obtener($localidadid,$usuarioid){
    $sql=" select id,comentario
             from producciontemp
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";
    return $sql;
}
function db_producciontemp_detalle_listar($localidadid,$usuarioid){
    $sql=" select id,productoid,descripcion,cantidad
             from producciontemp_detalle
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1
             order by fecha_hora_creacion desc";
    return $sql;
}
?>