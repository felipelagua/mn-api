<?php
function db_login_usuario_obtener($usuario){
    $sql=" select id,nombre ,usuario,clave
            from usuario 
            where usuario='$usuario' and activo=1";
    return $sql;
}
function db_login_localidad_listar($usuarioid){
    $sql="SELECT id,nombre,direccion
    FROM localidad
    WHERE activo=1
    AND id IN (SELECT localidadid FROM usuario_acceso WHERE usuarioid='$usuarioid')
    ORDER BY nombre";
    return $sql;
}
function db_login_grupo_obtener($usuarioid,$localidadid){
    $sql="SELECT a.localidadid,a.grupousuarioid
    FROM usuario_acceso AS a
    INNER JOIN localidad AS b ON b.id=a.localidadid
    WHERE a.usuarioid='$usuarioid'
    and a.localidadid='$localidadid'
    AND a.activo=1
    AND b.activo=1";
    return $sql;
}
function db_login_permiso_listar($grupousuarioid){
    $sql="SELECT permisoid
    FROM grupousuario_acceso AS a
    WHERE a.grupousuarioid='$grupousuarioid'
    AND a.activo=1";
    return $sql;
}
?>