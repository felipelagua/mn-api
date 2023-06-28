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
?>