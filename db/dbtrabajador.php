<?php

function db_trabajador_listar($nombre){
    $sql="SELECT a.id,a.nombre,a.sueldo,IFNULL(b.nombre,'') AS localidad_nombre
    from trabajador a
    LEFT JOIN localidad AS b ON b.id=a.localidadid
    WHERE a.activo=1
    and a.nombre like  '%".$nombre."%'";
    return $sql;
}
function db_trabajador_obtener($id){
    $sql=" select a.id,a.nombre,a.dni,a.sueldo,a.localidadid,
    b.nombre as localidad_nombre
    from trabajador as a
    inner join localidad as b on b.id=a.localidadid
    where a.id='$id' and a.activo=1";
    return $sql;
}
?>