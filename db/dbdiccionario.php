<?php
function db_diccionario_listar(){
    $sql="select dice,quizodecir
    from diccionario
    where activo=1";
    return $sql;
}

?>