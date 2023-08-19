<?php
    class DFormapago extends Model{
        private $table="formapago";
        public function registrar($o){
            $this->validarCaja($o);
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,caja,imagen from ".$this->table." where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,caja,imagen from ".$this->table." where id='@id' and activo=1";
            $this->sqlGet($sql,$o->id);
        }
        private function validarCaja($o){
            if($o->caja=="SI"){
                $sql=" select id,nombre,caja from ".$this->table." where id!='$o->id' and activo=1 and caja='SI'";
                $dt=$this->sqldata($sql);
                if(count($dt)>0){
                    $message="Existe una forma de pago con marcado CAJA";
                    $this->gotoError($message);
                }
            }
        }
    }
?>