
<?php
function db_pedido_insertar(
    $id,
    $localidadid,
    $clienteid,
    $tipopedido,
    $ubicacionid,
    $numero,
    $total,
    $pago,
    $saldo,
    $usuarioid,
    $hoy){
    $sql="insert into pedido(id,localidadid,clienteid,tipopedido,ubicacionid,numero,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
    values('$id','$localidadid', '$clienteid','$tipopedido','$ubicacionid','$numero',
    '$total','$pago','$saldo',1,'$usuarioid',".$hoy.")";
    return $sql;
}
function db_pedido_obtener_nuevo_numero(){
    $sql="SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 as numero from pedido";
    return $sql;
}
?>