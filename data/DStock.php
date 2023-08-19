<?php
using("db/stock");
usingdb("localidad");
usingdb("usuario");
usingdb("caja");
    class DStock extends Model{
        private function obtenerCajaid(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $dt=$this->sqldata(db_caja_abierta_obtener($localidadid,$usuarioid));
            $cajaid="";
            if(count($dt)>0){
                $cajaid=$dt[0]["id"];
            }
            return $cajaid;
        }
        public function listar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local(); 
            $cajaid="";   
            $hoy=now(); 
            if($o->turno=="SI"){
                $cajaid=$this->obtenerCajaid();
            }    
              
            $user=$this->sqlrow(db_usuario_obtener($usuarioid));
            $data["cabecera"]=$this->sqlrow(db_localidad_obtener($localidadid));
            $data["cabecera"]["usuario_nombre"]=$user["usuario"];
            $data["detalle"]=$this->sqldata(db_stock_listar($localidadid,$o->venta,$o->movhoy,$o->nombre,$hoy,$o->stock,$o->otro,$cajaid));
            $data["impresora"]=$this->sqlrow(db_localidad_impresora_obtener($localidadid));
            $data["filename"]="MNSTK";
            
            return $data;
        }
        public function listarStock($o){
            $usuarioid=auth::user();     
            $hoy=now();        
            $user=$this->sqlrow(db_usuario_obtener($usuarioid));
            $data["cabecera"]=$this->sqlrow(db_localidad_obtener($o->localidadid));
            $data["cabecera"]["usuario_nombre"]=$user["usuario"];
            $data["detalle"]=$this->sqldata(db_stock_listar($o->localidadid,$o->venta,$o->movhoy,$o->nombre,$hoy,$o->stock,$o->otro,""));
            $data["impresora"]=$this->sqlrow(db_localidad_impresora_obtener($o->localidadid));
            $data["filename"]="MNSTK";
            return $data;
        }
        public function obtenerFiltros(){
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data); 
 
        }
        public function obtenerLocalidad($localidadid){                       
            $sql=db_localidad_obtener($localidadid);
            return $this->sqlrow($sql);
        }
        
        public function obtener($o){
            $localidadid=auth::local();
            $sql=db_stock_obtener($localidadid,$o->id);
            $sqldet=db_stock_detalle_listar($localidadid,$o->productoid);
            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
        public function obtenerDetalleStock($o){
            $sql=db_stock_obtener($o->localidadid,$o->id);
            $sqldet=db_stock_detalle_listar($o->localidadid,$o->productoid);
            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
        public function obtenerStock($o){                      
            $row= $this->sqlrow(db_stock_obtener($o->id));
            $this->gotoSuccessData($row);
        }
        public function registrarStock($o){
            $hoy=now();$usuarioid=auth::user(); 
            $sql="update localidad_producto 
            set stock_minimo='$o->stock_minimo',stock_maximo='$o->stock_maximo',
            usuario_modificacion='$usuarioid',fecha_hora_modificacion=$hoy
            where id='$o->id'";
            
            $this->db->execute($sql);
            $this->gotoSuccess("Se grabaron los datos con éxito","");
            
        }
    }
?>