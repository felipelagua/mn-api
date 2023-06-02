
<?php
    function db_stock_listar($localidadid,$nombre){
        $sql="SELECT b.id,b.nombre,convert(a.cantidad,decimal(10,0)) as cantidad
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
?>