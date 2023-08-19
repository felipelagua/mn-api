

<?php
    function db_tipopedido_listar(){
        $sql=" select id,nombre,clave,ubicacion,delivery,icono,color
        from tipopedido 
        where activo=1 ";
        return $sql;
    }
?>
