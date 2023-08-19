<?php
function db_compra_insertar($id,$numero,$localidadid,$tipocomprobanteid,$proveedorid,$total,$pago,$saldo,$usuarioid,$hoy){
    $sql=" 
    insert into compra(id,numero,localidadid,tipocomprobanteid,proveedorid,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
    select '$id','".$numero."','$localidadid','".$tipocomprobanteid."',
    '".$proveedorid."',
    '".$total."',
    '".$pago."',
    '".$saldo."',
    1,'$usuarioid',$hoy ";
    return $sql;
}
function db_compra_servicio_insertar($id,$servicioid,$periodoservicio,$numero,$localidadid,$tipocomprobanteid,$proveedorid,$total,$pago,$saldo,$usuarioid,$hoy){
    $sql=" 
    insert into compra(id,servicioid,periodoservicio,numero,localidadid,tipocomprobanteid,proveedorid,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
    select '$id','$servicioid','$periodoservicio','".$numero."','$localidadid','".$tipocomprobanteid."',
    '".$proveedorid."',
    '".$total."',
    '".$pago."',
    '".$saldo."',
    1,'$usuarioid',$hoy ";
    return $sql;
}
function db_compra_detalle_insertar($correlativo,$compraid,$localidadid,$productoid,$descripcion,$cantidad,$precio,$importe,$usuarioid,$hoy){
    $sql="insert into compra_detalle(id,correlativo,compraid,localidadid,productoid,descripcion,cantidad,precio,importe,activo,usuario_creacion,fecha_hora_creacion)
    values(uuid(),$correlativo,'$compraid','$localidadid','".$productoid."','".$descripcion."',
    '".$cantidad."',
    '".$precio."',
    '".$importe."',
    1,'$usuarioid',$hoy)";
    return $sql;
}
function db_compra_pago_insertar($correlativo,$compraid,$localidadid,$cuentaid,$descripcion,$pago,$usuarioid,$hoy){
    $sql="insert into compra_pago(id,correlativo,compraid,localidadid,cuentaid,descripcion,pago,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$compraid','$localidadid','".$cuentaid."','".$descripcion."','".$pago."',1,'$usuarioid',$hoy)";
    return $sql;
}
?>