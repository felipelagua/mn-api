<?php

    class DLogin extends Model{
 
        public function autenticar($o){
            $this->existe($o);
            $sql=" select id,nombre ,usuario,clave
            from usuario 
            where usuario='$o->usuario' and activo=1";

            $row=$this->sqlgetrow($sql);
            if($row["clave"]!=$o->clave){
                $this->gotoError("El Usuario o la contraseña no es válida");
            }
            $data["usuario"]=$row;
            return $data;
        }
        private function existe($o){
            $sql=" select id
            from usuario
             where usuario='$o->usuario' and activo=1";
            $dt=$this->sqldata($sql);
            if(count($dt)==0){
                $this->gotoError("El Usuario o la contraseña no es válida");
            }
        }
        public function generateSesion($o){
            $this->gotoSuccessData($o);
        }
        public function listarLocalidad($id){
            $sql="SELECT id,nombre,direccion
            FROM localidad
            WHERE activo=1
            AND id IN (SELECT localidadid FROM usuario_acceso WHERE usuarioid='$id')
            ORDER BY nombre";
            $this->sqlread($sql);
        }
        public function obtenerGrupo($usuarioid,$localidadid){
            $sql="SELECT a.localidadid,a.grupousuarioid
            FROM usuario_acceso AS a
            INNER JOIN localidad AS b ON b.id=a.localidadid
            WHERE a.usuarioid='$usuarioid'
            and a.localidadid='$localidadid'
            AND a.activo=1
            AND b.activo=1";
            $row=$this->sqlgetrow($sql);
            return $row;
        }
        public function listarPermisos($grupousuarioid){
            $sql="SELECT permisoid
            FROM grupousuario_acceso AS a
            WHERE a.grupousuarioid='$grupousuarioid'
            AND a.activo=1";
            $data=$this->sqldata($sql);

            $array = array();
            foreach($data as $row){
                array_push($array,$row["permisoid"]);

            }
            return $array;
        }
    }
?>