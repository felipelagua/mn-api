<?php
usingdb("login");
usingdb("diccionario");
    class DLogin extends Model{
 
        public function autenticar($o){
            $this->existe($o);
            $row=$this->sqlrow(db_login_usuario_obtener($o->usuario));
            if($row["clave"]!=$o->clave){
                $this->gotoError("El Usuario o la contraseña no es válida 2");
            }
            $data["usuario"]=$row;
            return $data;
        }
        public function cambiarClave($o){
            $usuarioid=auth::user();
            $hoy=now();
            $sql="update usuario set clave='$o->nuevaclave',fecha_hora_modificacion=$hoy
            where id='$usuarioid'";
            $this->db->execute($sql);
            $this->gotoSuccess("Se procesaron los datos con éxito. Inicie sesión nuevamente","");

        }
        private function existe($o){
            $dt=$this->sqldata(db_login_usuario_obtener($o->usuario));
            if(count($dt)==0){
                $this->gotoError("El Usuario o la contraseña no es válida 1");
            }
        }
        public function listarLocalidad(){
            $usuarioid=auth::user();
            $dt= $this->sqldata(db_login_localidad_listar($usuarioid ));
            return $dt;
        }
        public function listarDiccionario(){
            $dt= $this->sqldata(db_diccionario_listar());
            return $dt;
        }
        public function obtenerGrupo($usuarioid,$localidadid){
            $row=$this->sqlrow(db_login_grupo_obtener($usuarioid,$localidadid));
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