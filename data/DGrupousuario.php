<?php
    class DGrupousuario extends Model{
        private $table="grupousuario";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre from ".$this->table." where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
        public function obtenerAcceso($o){
            $sql=" select id,nombre from ".$this->table." where id='$o->id' and activo=1";
            $sqlsec=" select id,codigo,nombre 
            from permiso
             where activo=1 
             and length(codigo)=2
            order by codigo ";

            $data["cabecera"]= $this->sqlgetrow($sql);
            $data["secciones"]= $this->sqldata($sqlsec);
            if(count($data["secciones"])>0){
                $i=0;
                foreach($data["secciones"] as $sec){
                    $sql=" select a.id,a.icono,a.nombre ,case when b.id is null then 'NO' else 'SI' end as activado
                    from permiso as a
                    left join grupousuario_acceso as b on b.grupousuarioid='$o->id' and b.permisoid=a.id and b.activo=1
                     where left(a.codigo,2)='".$sec["codigo"]."'
                     and length(a.codigo)=4
                     and a.activo=1
                     order by codigo";
                     $data["secciones"][$i]["detalle"]=$this->sqldata($sql);
                     $i++;
                }
            }
            $this->gotoSuccessData($data);
        }
        public function modificarAcceso($o){
            if($o->activado=="NO"){
                $this->eliminarPermiso($o);
            }
            else{
                if(!$this->existePermiso($o)){
                    $this->insertarPermiso($o);
                }
            }
            
        }
        private function existePermiso($o){
            $sql=" select id from grupousuario_acceso
             where grupousuarioid='$o->grupousuarioid'
             and permisoid='$o->permisoid' 
             and activo=1";
            $dt = $this->sqldata($sql,$o->id); 
            $state= count($dt)>0?true:false;
            return $state;
        }
        private function eliminarPermiso($o){
            $sql=" delete from grupousuario_acceso
            where grupousuarioid='$o->grupousuarioid'
            and permisoid='$o->permisoid' 
            and activo=1";
            $this->db->execute($sql);
            $this->gotoSuccess("Se eliminaron los datos con éxito",$o->id);
        }
        private function insertarPermiso($o){
            $usuarioid=auth::user();
            $hoy=now();
            $o->id=guid();
            $sql="insert into grupousuario_acceso(id,grupousuarioid,permisoid,activo,usuario_creacion,fecha_hora_creacion)
            values('$o->id','$o->grupousuarioid','$o->permisoid',1,'$usuarioid',$hoy)
            ";
            $this->db->execute($sql);
            $this->gotoSuccess("Se grabaron los datos con éxito",$o->id);

        }
    }
?>