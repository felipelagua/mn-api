<?php
using("db/diccionario");
    class DDiccionario extends Model{
        private $table="diccionario";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $this->sqlread(db_diccionario_listar($o->dice));
        }
        public function obtener($o){
            $sql=" select id,dice,quizodecir from diccionario where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
    }
?>