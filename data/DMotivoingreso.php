<?php
    class DMotivoingreso extends Model{
        private $table="motivoingreso";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new EMotivoingreso(null);
            $this->all($this->table,$ent);
        }
    }
?>