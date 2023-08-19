<?php
function db_producto_listar($nombre,$clasificacion1id){
    $sql=" select a.id,a.nombre,a.nombre_web,a.descripcion,a.stock,a.venta,
            b.nombre as clasificacion1_nombre,
            c.nombre as clasificacion2_nombre,
            d.nombre as clasificacion3_nombre,a.deshabilitado,
            a.precio_venta
            from producto as a
            left join clasificacion1 as b on b.id=a.clasificacion1id
            left join clasificacion2 as c on c.id=a.clasificacion2id
            left join clasificacion3 as d on d.id=a.clasificacion3id
            where a.activo=1 
            and ('$clasificacion1id'='' or a.clasificacion1id='$clasificacion1id')
            and ('$nombre'='' or a.nombre like  '%".$nombre."%')
            order by a.nombre";
            return $sql;
}
    function db_reporte_producto_listar(){
        $sql="SELECT a.nombre,
        b.nombre AS clasificacion1_nombre,
        ifnull(c.nombre,'') AS clasificacion2_nombre,
        ifnull(d.nombre,'') AS clasificacion3_nombre,a.terminado,
        a.stock,a.instantaneo,a.compra,a.venta,
        a.precio_compra,a.precio_venta
        FROM producto AS a
        INNER JOIN clasificacion1 AS b ON b.id=a.clasificacion1id
        LEFT JOIN clasificacion2 AS c ON  c.id=a.clasificacion2id
        LEFT JOIN clasificacion3 AS d ON d.id=a.clasificacion3id
        WHERE a.activo=1
        ORDER BY a.nombre";
        return $sql;
    }
    function db_reporte_producto_venta_listar(){
        $sql="SELECT a.nombre,
        b.nombre AS clasificacion1_nombre,
        ifnull(c.nombre,'') AS clasificacion2_nombre,
        ifnull(d.nombre,'') AS clasificacion3_nombre,terminado,
        a.stock,a.instantaneo,a.compra,a.venta,
        a.precio_compra,a.precio_venta
        FROM producto AS a
        INNER JOIN clasificacion1 AS b ON b.id=a.clasificacion1id
        LEFT JOIN clasificacion2 AS c ON  c.id=a.clasificacion2id
        LEFT JOIN clasificacion3 AS d ON d.id=a.clasificacion3id
        WHERE a.activo=1 and a.venta='SI'
        ORDER BY a.nombre";
        return $sql;
    }
    function db_reporte_producto_instantaneo_listar(){
        $sql="SELECT a.nombre,
        b.nombre AS clasificacion1_nombre,
        ifnull(c.nombre,'') AS clasificacion2_nombre,
        ifnull(d.nombre,'') AS clasificacion3_nombre,a.terminado,
        a.stock,a.instantaneo,a.compra,a.venta,
        a.precio_compra,a.precio_venta
        FROM producto AS a
        INNER JOIN clasificacion1 AS b ON b.id=a.clasificacion1id
        LEFT JOIN clasificacion2 AS c ON  c.id=a.clasificacion2id
        LEFT JOIN clasificacion3 AS d ON d.id=a.clasificacion3id
        WHERE a.activo=1 and a.instantaneo='SI'
        ORDER BY a.nombre";
        return $sql;
    }
    function db_reporte_producto_terminado_listar(){
        $sql="SELECT a.nombre,
        b.nombre AS clasificacion1_nombre,
        ifnull(c.nombre,'') AS clasificacion2_nombre,
        ifnull(d.nombre,'') AS clasificacion3_nombre,a.terminado,
        a.stock,a.instantaneo,a.compra,a.venta,
        a.precio_compra,a.precio_venta
        FROM producto AS a
        INNER JOIN clasificacion1 AS b ON b.id=a.clasificacion1id
        LEFT JOIN clasificacion2 AS c ON  c.id=a.clasificacion2id
        LEFT JOIN clasificacion3 AS d ON d.id=a.clasificacion3id
        WHERE a.activo=1 and a.terminado='SI'
        ORDER BY a.nombre";
        return $sql;
    }

    function db_producto_actualizar_precio_compra($productoid,$precio_compra){
        $sql="update  producto set
        precio_compra ='$precio_compra' 
        where id='$productoid' ";
        return $sql;
    }
    function db_producto_buscar($nombre){
        $sql=" select id as productoid,nombre as descripcion
            from producto 
            where activo=1 
            and compra='SI'
            and nombre like  '%".$nombre."%'
            order by fecha_hora_creacion desc";
        return $sql;
    }
    function db_producto_venta_buscar($nombre){
        $sql=" select id as productoid,nombre as descripcion
            from producto 
            where activo=1 
            and venta='SI'
            and nombre like  '%".$nombre."%'
            order by fecha_hora_creacion desc";
        return $sql;
    }
    function db_producto_stock_buscar($localidadid,$nombre){
        $sql=" select a.id as productoid,a.nombre as descripcion,1 as cantidad ,a.precio_venta as precio,
        cast(ifnull(case when a.instantaneo='SI' then 999 else b.cantidad end,0) as decimal(10,2)) as cantidad_stock,
        a.importecaja
            from producto as a
            left join localidad_producto as b on b.productoid=a.id and b.localidadid='$localidadid' and b.activo=1
            where a.activo=1 
            and a.venta='SI'
            and nombre like  '%".$nombre."%'
            order by a.fecha_hora_creacion desc";
        return $sql;
    }
    function db_producto_stock_buscar_nombre($localidadid,$nombre){
        $sql=" select a.id as productoid,a.nombre as descripcion,1 as cantidad ,a.precio_venta as precio,
        cast(ifnull(case when a.instantaneo='SI' then 999 else b.cantidad end,0) as decimal(10,2)) as cantidad_stock,
        a.importecaja
            from producto as a
            left join localidad_producto as b on b.productoid=a.id and b.localidadid='$localidadid' and b.activo=1
            where a.activo=1 
            and a.venta='SI'
            and nombre =  '".$nombre."'
            order by a.fecha_hora_creacion desc";
        return $sql;
    }
    function db_producto_insumo_listar($localidadid,$productoid){
        $sql = "select a.id,a.itemid as productoid,b.nombre AS descripcion,a.cantidad,
        case when c.id is null then 'N' else 'S' end as locprod,
        case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
        b.stock,ifnull(c.precio,0.00) as precio_stock
        FROM producto_insumo AS a
        inner join producto as b on b.id=a.itemid
        left join localidad_producto as c on c.productoid=a.itemid and c.localidadid='$localidadid'
        WHERE a.productoid = '".$productoid."'
        AND a.activo = 1";
        return $sql;
    }
?>