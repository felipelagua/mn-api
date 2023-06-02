

<?php
    function db_tipopedido_listar(){
        $sql=" select id,nombre,clave,ubicacion,delivery,icono
        from tipopedido 
        where activo=1 ";
        return $sql;
    }
?>
