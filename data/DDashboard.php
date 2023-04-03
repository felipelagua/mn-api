<?php
    class DDashboard extends Model{
       
        public function listarAcceso(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $grupousuarioid=auth::grupo();

            $sql="SELECT id,codigo,nombre,icono
            FROM permiso 
            WHERE activo=1
            AND codigo IN (
            SELECT distinct left(b.codigo,2)
            FROM grupousuario_acceso AS a
            INNER JOIN permiso AS b ON b.id=a.permisoid
            WHERE a.grupousuarioid='$grupousuarioid'
            AND a.activo=1
            AND b.activo=1)
            ORDER  BY codigo";
            
            $dtcab=$this->sqldata($sql);
            $index=0;
            foreach($dtcab as $row){
                $codigo=$row["codigo"];
                $sql="
                SELECT   codigo,nombre,icono,url
                FROM grupousuario_acceso AS a
                INNER JOIN permiso AS b ON b.id=a.permisoid
                WHERE a.grupousuarioid='$grupousuarioid'
                AND a.activo=1 and left(b.codigo,2)='$codigo'
                AND b.activo=1 
                ORDER  BY codigo";
                $dtcab[$index]["detalle"]=$this->sqldata($sql);
                $index++;
            }
            
            $sql="select nombre from usuario where id='$usuarioid'";
            $sqlloc="select nombre from localidad where id='$localidadid'";

            $data["usuario"]=$this->sqlgetrow(($sql));
            $data["localidad"]=$this->sqlgetrow(($sqlloc));
            $data["secciones"]=$dtcab;

            $this->gotoSuccessData($data);
        }
 
    }
?>