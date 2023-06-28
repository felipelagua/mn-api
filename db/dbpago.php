<?php
function db_pago_periodo_listar($anio,$mes){
    $sql="SELECT a.id,a.nombre,a.sueldo,
    case when NOT b.id IS NULL then 'SI' ELSE 'NO' END AS pagado
    FROM trabajador AS a
    LEFT JOIN pago AS b ON b.trabajadorid=a.id AND YEAR(b.periodo)='$anio' AND MONTH(b.periodo)='$mes' 
    WHERE a.activo=1;";
    return $sql;
}
function db_pago_insertar($id,$localidadid,$trabajadorid,$anio,$mes,$cuentaid,$total,$usuarioid,$hoy){
    $periodo=$anio."-".$mes."-01";
    $sql="insert into pago(id,localidadid,trabajadorid,periodo,cuentaid,total,activo,usuario_creacion,fecha_hora_creacion)
    values('$id','$localidadid','$trabajadorid','$periodo','$cuentaid','$total',1,'$usuarioid',$hoy)";
    return $sql;
}
function db_pago_obtener($trabajadorid,$anio,$mes){
    $sql="select id 
    from pago
    where trabajadorid='$trabajadorid' 
    and year(periodo)='$anio' 
    and month(periodo)='$mes'";
    return $sql;
}
?>