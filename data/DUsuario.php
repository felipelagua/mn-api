<?php
    class DUsuario extends Model{
        private $table="usuario";
        public function registrar($o){
            $excludes = array("usuario");
            $this->saveWithExclude($this->table,$o, $excludes);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,usuario,nombre from usuario where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,usuario,nombre from usuario where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>