
<?php
    function db_ubicacion_listar($localidadid){
        $sql=" select id ,nombre,'bi bi-bell' as icono
                from ubicacion 
                where localidadid='$localidadid' 
                and activo=1
                order by nombre";
        return $sql;
    }
    function db_ubicacion_listar_estado($localidadid){
        $sql=" select a.id ,a.nombre,'bi bi-bell' as icono,
        case when b.id is null then 
            'btn-success'
        else
            'btn-danger'
        end as bgcolor,case when b.id is null then 'LIBRE' else 'OCUPADO' end as estado 
                from ubicacion as a
                left join pedido as b on b.ubicacionid=a.id
                where a.localidadid='$localidadid' 
                and a.activo=1
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