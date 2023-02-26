<?php
    class DUsuario extends Model{
        private $table="usuario";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new EUsuario(null);
            $this->all($this->table,$ent);
        }
    }
?>