<?php
function db_venta_producto_listar($anio,$mes){
    $sql ="SELECT a.localidadid,c.nombre,SUM(importe) AS total 
    FROM venta_detalle AS a
    INNER JOIN producto AS b ON b.id=a.productoid
    INNER JOIN clasificacion1 AS c ON c.id=b.clasificacion1id
    WHERE b.venta='SI' and c.otro='NO'
    and date_format(a.fecha_hora_creacion,'%Y') = '$anio'
    and date_format(a.fecha_hora_creacion,'%m') = '$mes'
    GROUP BY a.localidadid,c.nombre
    ORDER BY 2 desc";
    return $sql;
}
function db_venta_pago_listar($ventaid){
    $sql ="SELECT a.descripcion,a.pago
    FROM venta_pago as a
    where a.ventaid='$ventaid'
    and a.activo=1
    ORDER BY a.fecha_hora_creacion";
    return $sql;
}
function db_venta_localidad_mes_listar($anio,$mes){
    $sql="SELECT a.localidadid as id,b.nombre,b.nombrecorto,SUM(total) AS total,count(a.id) as totalventas
    from venta a
    INNER JOIN localidad AS b ON b.id=a.localidadid
    where   date_format(a.fecha_hora_creacion,'%Y') = '$anio'
    and date_format(a.fecha_hora_creacion,'%m') = '$mes'
    GROUP BY b.nombre,b.nombrecorto
    ORDER BY b.nombrecorto";

    return $sql;
}
function db_venta_mes_total_listar($anio,$mes){
    $sql = "select 
    concat(
    case WEEKDAY(fecha_hora_creacion)
when 0 then 'Lu'
when 1 then 'Ma'
when 2 then 'Mi'
when 3 then 'Ju'
when 4 then 'Vi'
when 5 then 'Sa'
when 6 then 'Do' ELSE '' END,' ',
    date_format(fecha_hora_creacion,'%d')) as fecha ,sum(total) as total,localidadid
    
    from venta a
    where activo=1
    and date_format(a.fecha_hora_creacion,'%Y') = '$anio'
    and date_format(a.fecha_hora_creacion,'%m') = '$mes'
    group by localidadid,date_format(fecha_hora_creacion,'%d/%m/%Y')
    ORDER BY date_format(fecha_hora_creacion,'%Y-%m-%d') asc";
    return $sql;
}
function db_venta_mes_count_listar($anio,$mes){
    $sql = "select 
    concat(
        case WEEKDAY(fecha_hora_creacion)
    when 0 then 'Lu'
    when 1 then 'Ma'
    when 2 then 'Mi'
    when 3 then 'Ju'
    when 4 then 'Vi'
    when 5 then 'Sa'
    when 6 then 'Do' ELSE '' END,' ',
        date_format(fecha_hora_creacion,'%d')) as fecha ,count(1) as total,localidadid
    from venta a
    where activo=1
    and date_format(a.fecha_hora_creacion,'%Y') = '$anio'
    and date_format(a.fecha_hora_creacion,'%m') = '$mes'
    group by localidadid,date_format(fecha_hora_creacion,'%d/%m/%Y')
    ORDER BY date_format(fecha_hora_creacion,'%Y-%m-%d') asc";
    return $sql;
}
function db_venta_mes_hora_listar($anio,$mes){
    $sql="select 
    cast(cast(date_format(b.fecha_hora_creacion,'%d') AS DECIMAL(10,2))+cast(date_format(b.fecha_hora_creacion,'%H') AS DECIMAL(10,2))/24 AS DECIMAL(10,2)) as fecha ,
cast(cast(date_format(b.fecha_hora_creacion,'%H') AS DECIMAL(10,2))+cast(date_format(b.fecha_hora_creacion,'%i') AS DECIMAL(10,2))/60 AS DECIMAL(10,2))  as hora,
a.localidadid
    from venta a
    inner join pedido as b on b.id=a.pedidoid
    where a.activo=1
    and date_format(b.fecha_hora_creacion,'%Y') = '$anio'
    and date_format(b.fecha_hora_creacion,'%m') = '$mes' 
    ORDER BY date_format(b.fecha_hora_creacion,'%Y-%m-%d %H%i')";
    return $sql;
}
?>