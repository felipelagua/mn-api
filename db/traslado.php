<?php
function db_trasladotemp_detalle($localidadid,$usuarioid){
    $sql =" SELECT a.id,a.productoid,a.descripcion,a.cantidad,ifnull(a.pedido,'') as pedido,
    b.stock,IFNULL(c.cantidad,0.00) AS cantidad_stock,b.stock
    from trasladotemp_detalle AS a
    INNER JOIN producto AS b ON b.id=a.productoid
    LEFT JOIN localidad_producto AS c ON c.localidadid=a.localidadid AND c.productoid=a.productoid AND c.activo=1
    where a.localidadid='$localidadid' 
    and a.usuario_creacion='$usuarioid'
    and a.activo=1
    order by a.fecha_hora_creacion desc";
    return $sql;
}
function db_trasladotemp_obtener($localidadid,$usuarioid){
    $sql="select a.id,a.localidaddestinoid,a.solicitadoporid,a.comentario,b.nombre as localidaddestino_nombre,
    (SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 from traslado) as numero,
    c.nombre as localidad_nombre,ifnull(a.pedidocompraid,'') as pedidocompraid,
    (SELECT ifnull(SUM(y.precio_compra*x.cantidad),0.00)
    FROM trasladotemp_detalle AS x
    INNER JOIN producto y ON y.id=x.productoid
    WHERE x.localidadid=a.localidadid AND x.usuario_creacion = a.usuario_creacion) AS precio_traslado
    from trasladotemp as a 
    inner join localidad as b on b.id=a.localidaddestinoid
    INNER JOIN localidad as c on c.id=a.localidadid
     where a.localidadid='$localidadid' 
     and a.usuario_creacion='$usuarioid'
     and a.activo=1";
     return $sql;
}
function db_traslado_producto_buscar($localidadid,$usuarioid,$nombre){
    $sql=" select a.id as productoid,a.nombre as descripcion,1  as cantidad,
    cast(b.cantidad  AS DECIMAL(10,0)) as cantidad_stock
    from producto as a
    left join localidad_producto b on b.productoid=a.id and b.localidadid='$localidadid'
    
     where a.activo=1 
     and b.activo=1
     and not a.id in (select productoid from trasladotemp_detalle
     where localidadid='$localidadid' and usuario_creacion='$usuarioid')
     and a.nombre like  '%".$nombre."%'
     order by a.fecha_hora_creacion desc";
    return $sql;
}
?>