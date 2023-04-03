<?php
    class DUsuario extends Model{
        private $table="usuario";
        public function registrar($o){
            $excludes = array("usuario");
            $this->saveWithExclude($this->table,$o, $excludes);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,usuario,nombre from usuario where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,usuario,nombre from usuario where id='$o->id' and activo=1";
            $sqlloc=" select id,nombre from localidad where  activo=1 order by nombre";
            $sqlgrup=" select id,nombre from grupousuario where  activo=1 order by nombre";
            $sqlAcc=$this->getSqlListaAcceso($o);
            $data["datos"]=$this->sqlgetrow($sql);
            $data["localidades"]=$this->sqldata($sqlloc);
            $data["grupos"]=$this->sqldata($sqlgrup);
            $data["accesos"]=$this->sqldata($sqlAcc);
            $this->gotoSuccessData($data);
        }
        public function registrarAcceso($o){
            $this->validarAcceso($o);
            $usuarioid=auth::user(); 
            $hoy=now();
            $sql="insert into usuario_acceso(id,usuarioid,localidadid,grupousuarioid,activo,usuario_creacion,fecha_hora_creacion)
            values('$o->id','$o->usuarioid','$o->localidadid','$o->grupousuarioid',1,'$usuarioid',$hoy)";
            $this->db->execute($sql);
            $this->gotoSuccess("Se registrar los datos correctamente",$o->id);
        }
        private function validarAcceso($o){
            $existeLocalidad=$this->existeLocalidad($o);
            $details = array();
            if($existeLocalidad){
                array_push($details,"El local seleccionado ya fue registrado");
            }
            if(count($details)){
                $this->gotoErrorDetails("Ocurrieron algunos errores",$details); 
            }
        }
        private function existeLocalidad($o){
              $state=false;
              $sql="select id from usuario_acceso
              where usuarioid='$o->usuarioid' and localidadid='$o->localidadid' and activo=1";
              $dt=$this->sqldata($sql);
              if(count($dt)>0){ $state=true; }
              return $state;
        }
        public function listarAcceso($o){
            $sql=$this->getSqlListaAcceso($o);
            $this->sqlread($sql);
        }
        private function getSqlListaAcceso($o){
            $sql="SELECT a.id,b.nombre AS localidad_nombre,c.nombre AS grupousuario_nombre
            FROM usuario_acceso AS a
            INNER JOIN localidad AS b ON b.id=a.localidadid
            INNER JOIN grupousuario AS c ON c.id = a.grupousuarioid
            where a.usuarioid='$o->id'
            ORDER BY b.nombre";
            return $sql;
        }
        public function eliminarAcceso($o){
             $sql=" delete
             from usuario_acceso
             where id='$o->id' ";

             $this->db->execute($sql);
            $this->gotoSuccess("Se eliminó correctamente",$o->id); 
        }
    }
?>