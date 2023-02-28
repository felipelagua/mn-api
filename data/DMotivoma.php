<?php
    class DMotivoma extends Model{
        private $table="motivoma";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new EMotivoma(null);
            $this->all($this->table,$ent);
        }
    }
?>