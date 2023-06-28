<?php
function db_caja_pre_cierre_obtener($localidadid,$usuarioid,$hoy){
    $sql="SELECT a.id,a.saldo,b.nombre as localidad_nombre,date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_ini,
    date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_fin,
    date_format($hoy,'%d/%m/%Y %H:%i') as fecha_actual,
    c.nombre as usuario_nombre
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
    UNION ALL
    SELECT UUID() AS id,'SALDO CAJA' AS nombre,1 AS cantidad, a.saldo AS monto 
    FROM caja AS a
    WHERE a.id='$cajaid' AND a.activo=1";
    return $sql;
}
?>