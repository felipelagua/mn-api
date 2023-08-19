<?php
    function db_diccionario_listar($dice){
        $sql=" select id,dice,quizodecir 
        from diccionario 
        where activo=1 
        and (dice like  '%".$dice."%' or quizodecir like  '%".$dice."%')
        order by dice";
        return $sql;
    }
    function db_diccionario_obtener($id){
        $sql=" select id,dice,quizodecir 
        from diccionario 
        where id='$id' and activo=1";
        return $sql;
    }
?>