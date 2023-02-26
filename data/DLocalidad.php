<?php
    class DLocalidad extends Model{
        private $table="localidad";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new ELocalidad(null);
            $this->all($this->table,$ent);
        }
    }
?>