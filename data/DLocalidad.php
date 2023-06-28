<?php
    class DLocalidad extends Model{
        private $table="localidad";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,direccion,venta from ".$this->table." where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,direccion,venta,impresora,rutaimpresion 
            from localidad where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
        public function obtenerLocalidad($id){
            $sql=" select id,nombre,direccion,venta,impresora,rutaimpresion
            from localidad where id='$id' and activo=1";
            return $this->sqlrow($sql);
        }
    }
?>