<?php

function db_servicio_listar(){
    $sql="SELECT a.id,b.nombre AS proveedor_nombre,c.nombre AS producto_nombre,d.nombre AS localidad_nombre,
    date_format(a.vence,'%d/%m/%Y') as vence,a.monto
    FROM servicio AS a
    INNER JOIN proveedor AS b ON b.id=a.proveedorid
    INNER JOIN producto AS c ON c.id=a.productoid
    INNER JOIN localidad AS d ON d.id=a.localidadid
    WHERE a.activo=1";
    return $sql;
}
function db_servicio_obtener($id){
    $sql=" SELECT a.id,b.nombre AS proveedor_nombre,c.nombre AS producto_nombre,d.nombre AS localidad_nombre,
    a.vence,a.monto,a.fijo,a.proveedorid,a.productoid,a.localidadid,a.duracionmes,
    a.ultimopago
    FROM servicio AS a
    INNER JOIN proveedor AS b ON b.id=a.proveedorid
    INNER JOIN producto AS c ON c.id=a.productoid
    INNER JOIN localidad AS d ON d.id=a.localidadid
    WHERE a.id='$id' and a.activo=1";
    return $sql;
}
function db_servicio_periodo_listar($anio,$mes){
    $sql="SELECT a.id,b.nombre AS proveedor_nombre,c.nombre AS producto_nombre,d.nombre AS localidad_nombre,
    date_format(a.vence,'%d/%m/%Y') as vence,a.monto,a.ultimopago,
    case when e.id is null then 'NO' else 'SI' end as pagado
    FROM servicio AS a
    INNER JOIN proveedor AS b ON b.id=a.proveedorid
    INNER JOIN producto AS c ON c.id=a.productoid
    INNER JOIN localidad AS d ON d.id=a.localidadid
    left join compra as e on e.servicioid=a.id and year(e.periodoservicio)='$anio' and month(e.periodoservicio)='$mes'
    WHERE a.activo=1
    and year(a.vence)='$anio' and month(a.vence)='$mes'";
    return $sql;
}
?>