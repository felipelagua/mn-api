<?php
function db_localidad_producto_insertar($localidadid,$productoid,$descripcion,$tipo,$cantidad,$cantidad_stock,$precio_stock,$usuarioid,$hoy){
    $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,precio,activo,
                                        usuario_creacion,fecha_hora_creacion)
                                        values(uuid(),'$localidadid','".$productoid."','$descripcion','$tipo', '$cantidad','$cantidad_stock','$precio_stock',1,'$usuarioid',$hoy)";
                                       
    return $sql;
}
function db_localidad_producto_insertar_caja($localidadid,$cajaid,$productoid,$descripcion,$tipo,$cantidad,$cantidad_stock,$precio_stock,$usuarioid,$hoy){
    $sql="insert into localidad_producto_detalle(id,localidadid,cajaid,productoid,descripcion,tipo,cantidad,saldo,precio,activo,
                                        usuario_creacion,fecha_hora_creacion)
                                        values(uuid(),'$localidadid','$cajaid','".$productoid."','$descripcion','$tipo', '$cantidad','$cantidad_stock','$precio_stock',1,'$usuarioid',$hoy)";
                                       
    return $sql;
}
function db_localidad_producto_actualizar($localidadid,$productoid,$cantidad_stock,$precio_stock,$usuarioid,$hoy){
    $sql="update localidad_producto set
    cantidad= '$cantidad_stock' , precio='$precio_stock',
    usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
    where localidadid='$localidadid' and productoid='$productoid' ";
    return $sql;
}
?>