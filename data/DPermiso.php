<?php
    class DPermiso extends Model{
        private $table="permiso";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $like=" and (codigo like '".$o->nombre."%' or nombre like  '%".$o->nombre."%')";
            $sql=" select id,codigo,nombre,url,icono from ".$this->table." where activo=1 ".$like." order by codigo";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,codigo,nombre,url,icono from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>