
<?php
    function db_ubicacion_listar($localidadid){
        $sql=" select id ,nombre,'bi bi-bell' as icono
                from ubicacion 
                where localidadid='$localidadid' 
                and activo=1
                order by nombre";
        return $sql;
    }
    function db_ubicacion_obtener($ubicacionid){
        $sql=" select id,nombre,localidadid 
        from ubicacion
        where id='$ubicacionid' 
        and activo=1";
        return $sql;
    }
?>