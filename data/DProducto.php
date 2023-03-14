<?php
    class DProducto extends Model{
        private $table="producto";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,stock from producto where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,stock from producto where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>