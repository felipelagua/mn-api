<?php
function db_cuenta_pago_listar(){
    $sql="SELECT a.id,concat(a.nombre,' = S/ ',a.saldo) AS nombre
    FROM cuenta AS a
    WHERE a.activo=1
    AND a.saldo>0 
    and a.pago ='SI'
    ORDER BY a.nombre";
    return $sql;
}
function db_cuenta_actualizar($id,$nombre,$usuarioid,$venta,$pago,$formapagoid,$cuentacierreid,$hoy){
    $sql="update cuenta set 
    nombre='$nombre',
    venta='$venta',
    pago='$pago',
    usuarioid='$usuarioid',
    formapagoid='$formapagoid',
    cuentacierreid='$cuentacierreid',
    fecha_hora_modificacion=$hoy
    where id='$id'";
    return $sql;
}
function db_cuenta_obtener($id){
    $sql = "select a.id,a.nombre,b.nombre as usuario_nombre,a.usuarioid,a.formapagoid,
    c.nombre as formapago_nombre,a.saldo,a.saldo_inicial,a.venta,a.pago,
    case when a.cuentacierreid=null || a.cuentacierreid='' then 'X' else a.cuentacierreid end as cuentacierreid,
    ifnull(d.nombre,'') as cuentadestino_nombre
    from cuenta as a
    inner join usuario as b on b.id=a.usuarioid
    inner join formapago as c on c.id = a.formapagoid
    left join cuenta as d on d.id=a.cuentacierreid
    where a.id='$id' and a.activo=1";
    return $sql;
}
?>