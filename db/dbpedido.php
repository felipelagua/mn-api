
<?php
function db_pedido_listar($localidadid){
    $sql="select a.id,a.tipocomprobanteid,a.clienteid, a.total,a.pago,a.saldo,
    ifnull(b.nombre,'CLIENTE GENERICO') as cliente_nombre,
    ifnull(b.direccion,'SIN DIRECCION') as direccion,
    ifnull(c.nombre_corto,'SIN COMPROBANTE') as tipocomprobante_nombre,
    a.tipopedido,d.nombre AS tipopedido_nombre,
    a.ubicacionid,ifnull(e.nombre,'') as ubicacion_nombre,
    DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
    f.nombre as usuario_nombre,
    CONCAT('# ',a.numero,' - ',d.nombre) as numero_tipo,
    a.numero as numero
    from pedido as a
    INNER JOIN tipopedido as d ON d.clave=a.tipopedido
    left join cliente as b on b.id=a.clienteid
    left join tipocomprobante as c on c.id=a.tipocomprobanteid
    LEFT JOIN ubicacion AS e ON e.id=a.ubicacionid
    left join usuario as f on f.id=a.usuario_creacion
    where a.localidadid='$localidadid'  
    and a.activo=1 and ifnull(a.emitido,'')=''
    order by a.fecha_hora_creacion desc";
    return $sql;
}
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
function db_pedido_detalle_listar($localidadid,$pedidoid){
    $sql=" select id,productoid,descripcion,cantidad,precio,importe
    from pedido_detalle
    where localidadid='$localidadid' 
    and pedidoid='$pedidoid'
    and activo=1
    order by fecha_hora_creacion desc";
    return $sql;
}
function db_pedido_pago_listar($localidadid,$pedidoid){
    $sql=" select a.id,a.pedidoid,a.cuentaid,a.descripcion,a.monto,a.pago,a.vuelto,c.nombre as formapago_nombre,c.caja,
    c.imagen
    from pedido_pago as a
    inner join cuenta as b on b.id=a.cuentaid
    inner join formapago as c on c.id=b.formapagoid
    where a.localidadid='$localidadid' 
    and a.pedidoid ='$pedidoid'
    and a.activo=1
    order by a.fecha_hora_creacion asc";
    return $sql;
}
function db_pedido_obtener($localidadid,$pedidoid){
    $sql="select a.id,a.tipocomprobanteid,a.clienteid,a.numero,a.total,a.pago,a.saldo,
    ifnull(b.nombre,'CLIENTE GENERICO') as cliente_nombre,
    ifnull(b.direccion,'SIN DIRECCION') as direccion,
    ifnull(c.nombre_corto,'SIN COMPROBANTE') as tipocomprobante_nombre,
    a.tipopedido,d.nombre AS tipopedido_nombre,
    a.ubicacionid,ifnull(e.nombre,'SIN UBICACION') as ubicacion_nombre,
    DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
    f.nombre as usuario_nombre,'S/' AS moneda
    from pedido as a
    INNER JOIN tipopedido as d ON d.clave=a.tipopedido
    left join cliente as b on b.id=a.clienteid
    left join tipocomprobante as c on c.id=a.tipocomprobanteid
    LEFT JOIN ubicacion AS e ON e.id=a.ubicacionid
    left join usuario as f on f.id=a.usuario_creacion
    where a.localidadid='$localidadid' 
    and a.id='$pedidoid'
    and a.activo=1";
    return $sql;
}
function db_pedido_cuenta_listar($usuarioid){
    $sql="select a.id,a.nombre,b.caja,b.nombre as formapago_nombre,b.imagen
    from cuenta a
    INNER JOIN formapago AS b ON b.id=a.formapagoid
    where a.usuarioid='$usuarioid' 
    and a.activo=1 and b.caja='SI'
    union all
    SELECT a.id,a.nombre,b.caja,b.nombre as formapago_nombre,b.imagen
    FROM cuenta AS a
    INNER JOIN formapago AS b ON b.id=a.formapagoid
    WHERE a.venta='SI' AND b.caja='NO'
    AND a.activo=1";
    return $sql;
}
?>