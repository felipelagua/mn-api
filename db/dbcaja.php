<?php
function db_caja_pre_cierre_obtener($localidadid,$usuarioid,$hoy){
    $sql="SELECT a.id,a.saldo,b.nombre as localidad_nombre,date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_ini,
    date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_fin,
    date_format($hoy,'%d/%m/%Y %H:%i') as fecha_actual,
    c.nombre as usuario_nombre,a.estado
    FROM caja AS a
    inner join localidad as b on b.id=a.localidadid
    inner join usuario as c on c.id=a.usuarioid
    WHERE a.usuarioid='$usuarioid'
    and a.localidadid='$localidadid' and a.estado in  ('A','R') and a.activo=1";
    return $sql;
}
function db_caja_pre_cierre_detalle($cajaid){
    $sql = "SELECT UUID() AS id,'SALDO INICIAL' AS descripcion,1 AS cantidad, a.saldo_inicial AS monto 
    FROM caja AS a
    WHERE a.id='$cajaid' AND a.activo=1
    UNION ALL
    SELECT UUID() AS id,'INGRESO EFECTIVO',COUNT(a.id) AS cantidad,SUM(a.monto) AS monto
    FROM caja_detalle AS a
    WHERE a.cajaid='$cajaid' AND a.activo=1 AND a.tipo='ING'
    UNION ALL
    SELECT UUID() AS id,'SALIDA EFECTIVO',COUNT(a.id) AS cantidad,ifnull(SUM(a.monto),0.00) AS monto
    FROM caja_detalle AS a
    WHERE a.cajaid='$cajaid' AND a.activo=1 AND a.tipo='SAL'
   
    union all 
    select uuid(),concat('VENTA ',e.nombre),count(a.cuentaid) as cantidad,sum(a.pago) as monto from venta_pago as a 
    inner join venta as b on b.id=a.ventaid
    inner join pedido as c on c.id=b.pedidoid
    inner join cuenta as d on d.id=a.cuentaid
    inner join formapago as e on e.id=d.formapagoid  
    where b.cajaid='$cajaid'
    group by e.nombre
    UNION ALL
    select uuid(),'TOTAL VENTA',ifnull(count(a.cuentaid),0) as cantidad,ifnull(sum(a.pago),0.00) as monto 
    from venta_pago as a 
    inner join venta as b on b.id=a.ventaid
    where b.cajaid='$cajaid'
    UNION ALL
    SELECT UUID() AS id,'SALDO CAJA' AS nombre,1 AS cantidad, a.saldo AS monto 
    FROM caja AS a
    WHERE a.id='$cajaid' AND a.activo=1";
    return $sql;
}
function db_caja_abierta_obtener($localidadid,$usuarioid){
    $sql="select id from caja where usuarioid='$usuarioid' and localidadid='$localidadid' and estado in ('A','R') and activo=1";
    return $sql;
}
function db_caja_abierta_usuario_obtener($usuarioid){
    $sql="select id,localidadid,saldo from caja where usuarioid='$usuarioid' and estado in ('A') and activo=1";
    return $sql;
}
function db_caja_detalle_listar($cajaid){
    $sql="SELECT 
    DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
    DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y') AS fecha,
    DATE_FORMAT(a.fecha_hora_creacion,'%H:%i') AS hora,
    a.descripcion,a.monto,a.saldo,a.tipo
    FROM caja_detalle AS a
    WHERE a.cajaid='$cajaid'
    and a.activo=1
    ORDER BY a.fecha_hora_creacion DESC
    LIMIT 0,100";
    return $sql;
}
function db_caja_cierre_listar($cajaid){
    $sql="SELECT descripcion,cantidad,monto
    FROM caja_cierre
    WHERE cajaid='$cajaid' and activo=1";
    return $sql;
}
function db_caja_obtener($cajaid){
    $sql="SELECT a.id,a.numero,a.saldo_inicial,a.saldo,
    b.nombre as localidad_nombre,c.nombre AS usuario_nombre,
    date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_ini,
    date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_fin,
    case when a.estado='A' then 'ABIERTO' ELSE 'CERRADO' END AS estado
    FROM caja AS a
    inner join localidad as b on b.id=a.localidadid
    INNER JOIN usuario AS c ON c.id=a.usuarioid
    where a.id='$cajaid'
    and a.activo=1";
    return $sql;
}
?>