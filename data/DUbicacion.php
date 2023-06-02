<?php
usingdb("ubicacion");
usingdb("localidad");
    class DUbicacion extends Model{
        private $table="ubicacion";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select a.id,a.nombre,a.localidadid ,b.nombre as localidad_nombre,'bi bi-bell' as icono
            from ubicacion as a
            inner join localidad as b on b.id=a.localidadid
             where a.activo=1 
             and a.nombre like  '%".$o->nombre."%'
             order by b.nombre,a.nombre";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $data["ubicacion"]=$this->sqlrow(db_ubicacion_obtener($o->id)); 
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data);
        }
        public function obtenerListas(){
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data);
        }
    }
?>