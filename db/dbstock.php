<?php
    function db_stock_listar($localidadid,$nombre){
        $sql="SELECT b.id,b.nombre,convert(a.cantidad,decimal(10,2)) as cantidad
        FROM localidad_producto AS a
        INNER JOIN producto AS b ON b.id=a.productoid
        WHERE a.localidadid = '$localidadid'
        AND b.stock='SI'
        AND a.activo=1
        AND b.activo=1
        and b.nombre like  '%".$nombre."%'
        order by b.nombre";
        return $sql;
    }
    function db_stock_detalle_listar($localidadid,$productoid){
        $sql="SELECT 
        DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
        a.descripcion,a.tipo,convert(a.cantidad , decimal(10,2)) as cantidad,convert(a.saldo , decimal(10,2)) as saldo
        FROM localidad_producto_detalle AS a
        WHERE a.productoid='$productoid'
        and a.localidadid='$localidadid'
        ORDER BY a.fecha_hora_creacion DESC
        LIMIT 0,100";
        return $sql;
    }
    function db_stock_obtener($localidadid,$productoid){
        $sql=  $sql="SELECT b.id,b.nombre,convert(a.cantidad,decimal(10,2)) as cantidad
        FROM localidad_producto AS a
        INNER JOIN producto AS b ON b.id=a.productoid
        WHERE a.localidadid = '$localidadid'
        AND a.activo=1
        AND b.activo=1
        and b.id =  '$productoid'";
        return $sql;
    }
    function db_stock_detalle_insertar($localidadid,$productoid,$descripcion,$tipo,
        $cantidad_stock,$nuevo_saldo,$precio_stock,$usuarioid,$hoy){
        $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,precio,activo,
        usuario_creacion,fecha_hora_creacion)
        values(uuid(),'$localidadid','".$productoid."','$descripcion','$tipo', '$cantidad_stock','$nuevo_saldo',
        '$precio_stock',1,'$usuarioid',$hoy)";
        return $sql;
    }
    function db_stock_actualizar($localidadid,$productoid,$nuevo_saldo,$precio_stock,$usuarioid,$hoy){
        $sql="update localidad_producto set
        cantidad=$nuevo_saldo , 
        precio='$precio_stock',
        usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
        where localidadid='$localidadid' and productoid='".$productoid."' ";
        return $sql;
    }
?>