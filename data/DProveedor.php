<?php
    class DProveedor extends Model{
        private $table="proveedor";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,direccion from ".$this->table." where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,dni,celular,direccion,rubro,ruc,domicilio_fiscal,credito from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>