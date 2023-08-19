<?php
    function db_stock_listar($localidadid,$venta,$movhoy,$nombre,$hoy,$stock,$otro,$cajaid){
        $sql="SELECT a.id, b.id as productoid,b.nombre,convert(a.cantidad,decimal(10,2)) as cantidad,a.stock_minimo,a.stock_maximo,
        b.imagen
        FROM localidad_producto AS a
        INNER JOIN producto AS b ON b.id=a.productoid
        LEFT JOIN clasificacion1  as c on c.id=b.clasificacion1id
        WHERE a.localidadid = '$localidadid'
        AND b.stock='SI'
        AND a.activo=1
        AND b.activo=1 ";
        if($venta=="SI"){
            $sql.=" and b.venta='$venta' ";
        }
        if($otro!="SI"){
            $sql.=" and ifnull(c.otro,'NO')!='SI'";
        }
        
        if($stock!="" && $stock!="X"){
            switch($stock){
                case "S":
                    $sql.=" and a.cantidad>0 ";
                    break;
                case "N":
                    $sql.=" and a.cantidad<=0 ";
                    break;
                case "A":
                    $sql.=" and (a.cantidad>0 and a.cantidad<=a.stock_minimo)";
                    break;
                case "B":
                    $sql.=" and (a.stock_maximo>0 and a.cantidad>a.stock_maximo)";
                    break;
            }
 
        }
        if($movhoy=="SI"){
            $sql.=" and productoid in (select productoid
            from localidad_producto_detalle as x 
            where x.localidadid='$localidadid'
            and date_format(x.fecha_hora_creacion,'%d/%m/%Y') = date_format($hoy,'%d/%m/%Y')
            )";
        }
        if($cajaid!=""){
            $sql.=" and productoid in (select productoid
            from localidad_producto_detalle as x 
            where x.cajaid='$cajaid')";
        }
        $sql.=" and b.nombre like  '%".$nombre."%'
        order by b.nombre";
        return $sql;
    }
    function db_stock_detalle_listar($localidadid,$productoid){
        $sql="SELECT 
        DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
        DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y') AS fecha,
        DATE_FORMAT(a.fecha_hora_creacion,'%H:%i') AS hora,
        a.descripcion,a.tipo,convert(a.cantidad , decimal(10,2)) as cantidad,convert(a.saldo , decimal(10,2)) as saldo,
        ifnull(b.usuario,'') as usuario_nombre
        FROM localidad_producto_detalle AS a
        left join usuario as b on b.id=a.usuario_creacion
        WHERE a.productoid='$productoid'
        and a.localidadid='$localidadid'
        ORDER BY a.fecha_hora_creacion DESC
        LIMIT 0,100";
        return $sql;
    }
    function db_stock_obtener($id){
        $sql=  $sql="SELECT a.id,b.id as productoid,b.nombre,convert(a.cantidad,decimal(10,2)) as cantidad,a.stock_minimo,a.stock_maximo
        FROM localidad_producto AS a
        INNER JOIN producto AS b ON b.id=a.productoid
        WHERE   a.id =  '$id'  and a.activo=1
        AND b.activo=1
        ";
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