<?php
usingdb("stock");
usingdb("localidad");
    class DStock extends Model{
 
        public function listar($o){
            $localidadid=auth::local();            
            $sql=db_stock_listar($localidadid,$o->nombre);
            return $this->sqldata($sql);
        }

        public function obtenerLocalidad($localidadid){                       
            $sql=db_localidad_obtener($localidadid);
            return $this->sqlrow($sql);
        }
        
        public function obtener($o){
            $localidadid=auth::local();
            $sql=db_stock_obtener($localidadid,$o->id);
            $sqldet=db_stock_detalle_listar($localidadid,$o->id);
            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
        
    }
?>