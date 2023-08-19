<?php
    function db_usuario_obtener($id){
        $sql="select id,nombre,usuario
        from usuario
        where id='$id' and activo=1";
        return $sql;
    }
?>