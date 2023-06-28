<?php
function db_localidad_costo_insertar($localidadid,$documento,$documentoid,
                                    $descripcion,$tipo,$monto,$usuarioid,$hoy){

    $sql="INSERT INTO localidad_costo(id,localidadid,documento,documentoid,descripcion,tipo,monto,activo,usuario_creacion,fecha_hora_creacion)
    VALUES(uuid(),'$localidadid','$documento','$documentoid',
    '$descripcion','$tipo','$monto',1,'$usuarioid',$hoy)";
    return $sql;
}
?>