<?php
    class DMotivosalida extends Model{
        private $table="motivosalida";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new EMotivosalida(null);
            $this->all($this->table,$ent);
        }
    }
?>