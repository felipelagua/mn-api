<?php
usingdb("trabajador");
usingdb("localidad");
    class DTrabajador extends Model{
        private $table="trabajador";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql= db_trabajador_listar($o->nombre);
            $this->sqlread($sql);
        }
        public function obtener($o){
            $data["ent"]=new Etrabajador($this->sqlrow(db_trabajador_obtener($o->id)));
            if($data["ent"]->localidadid==""){$data["ent"]->localidadid="X";}
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data);
        }
        public function listarLocalidad(){
            $sql= db_localidad_listar();
            $this->sqlread($sql);
        }
    }
?>