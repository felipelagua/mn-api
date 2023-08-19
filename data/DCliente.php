<?php
    class DCliente extends Model{
        private $table="cliente";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,direccion 
            from cliente 
            where activo=1 and nombre like  '%".$o->nombre."%'
            order by nombre";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,dni,celular,direccion,generico,ruc,domicilio_fiscal,credito from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>