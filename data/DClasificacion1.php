<?php
    class DClasificacion1 extends Model{
        private $table="clasificacion1";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,otro from ".$this->table."
             where activo=1 and nombre like  '%".$o->nombre."%'
             order by nombre";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,otro from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>