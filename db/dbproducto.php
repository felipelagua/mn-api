<?php
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
?>