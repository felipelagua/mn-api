<?php
    class DProducto extends Model{
        private $table="producto";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new EProducto(null);
            $this->all($this->table,$ent);
        }
    }
?>