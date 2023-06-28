<?php
usingdb("servicio");
usingdb("producto");
usingdb("proveedor");
usingdb("localidad");
usingdb("cuenta");
usingdb("tipocomprobante");
    class DServicio extends Model{
        private $table="servicio";
        public function registrar($o){
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql= db_servicio_listar();
            $this->sqlread($sql);
        }
        public function obtener($o){
            $data["ent"]=new EServicioEnt($this->sqlrow(db_servicio_obtener($o->id)));
            if($data["ent"]->localidadid==""){$data["ent"]->localidadid="X";}
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data);
        }
        public function obtenerLista(){
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data);
        }
        public function buscarProveedor($o){
              $this->sqlread(db_proveedor_buscar($o->nombre));
        }
        public function buscarProducto($o){
            $this->sqlread(db_producto_buscar($o->nombre));
        }
        public function listarPeriodo($o){         
            $sql=db_servicio_periodo_listar($o->anio,$o->mes);
            return $this->sqldata($sql);
        }
        public function obtenerFiltros(){
            $hoy=now();
            
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format($hoy,'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM compra";
            $sqlanioactual="SELECT date_format($hoy,'%Y') AS id,date_format($hoy,'%Y') as nombre";

            $anios=$this->sqldata($sqlanio);
            $anioactual=$this->sqlgetrow($sqlanioactual);
            if(count($anios)==0){
                $anios[0]=$anioactual;
            }
         

            $data["meses"]=$this->sqldata($sqlmes);
            $data["anios"]=$anios;
            $data["mesactual"]=$this->sqlgetrow($sqlmesactual)["mes"] ;
            $data["anioactual"]= $anioactual["id"] ;
            $this->gotoSuccessData($data); 
 
        }
        public function obtenerDatosPago($o){
 
            $data["ent"]=$this->sqlrow(db_servicio_obtener($o->id));
            $data["cuenta"]=$this->sqldata(db_cuenta_pago_listar());
            $data["tipocomprobante"]=$this->sqldata(db_tipocomprobante_listar());
            $this->gotoSuccessData($data); 
 
        }
    }
?>