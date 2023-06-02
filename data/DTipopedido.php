<?php
usingdb("tipopedido");
    class DTipopedido extends Model{
        private $table="tipopedido";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=db_tipopedido_listar();
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,clave,ubicacion,delivery from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>