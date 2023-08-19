
<?php
function db_localidad_buscar($nombre){
    $sql=" select id,nombre,direccion 
    from localidad where activo=1 
    and nombre like  '%".$nombre."%'";
    return $sql;
}
function db_localidad_listar(){
    $sql=" select id,nombre,direccion,impresora,nombrecorto
    from localidad 
    where activo=1 
    order by nombre";
    return $sql;
}
function db_localidad_obtener($id){
    $sql=" select id,nombre,direccion,venta ,impresora,nombrecorto
    from localidad 
    where id='$id' and activo=1";
    return $sql;
}
function db_localidad_impresora_obtener($id){
    $sql=" select impresora
    from localidad 
    where id='$id' and activo=1";
    return $sql;
}
?>