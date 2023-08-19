<?php
usingdb("servicio");
usingdb("producto");
usingdb("proveedor");
usingdb("localidad");
usingdb("cuenta");
usingdb("compra");
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
        public function registrarPago($o){
            $hoy=now();
            $usuarioid=auth::user();$localidadid=auth::local();
            $o->id=guid();
            $serv=$this->sqlrow(db_servicio_obtener($o->servicioid));
            $cta=$this->sqlrow(db_cuenta_obtener($o->cuentaid));

            $periodoservicio=$o->anio."-".$o->mes."-01";
            
            $sql= db_compra_servicio_insertar($o->id,$o->servicioid,$periodoservicio,$o->numero,$serv["localidadid"],
            $o->tipocomprobanteid,$serv["proveedorid"],$o->monto,$o->monto,0,$usuarioid,$hoy);
            $array = array($sql);

            $sql=db_compra_detalle_insertar(1,$o->id,$serv["localidadid"],$serv["productoid"],$serv["producto_nombre"],1,$o->monto,$o->monto,$usuarioid,$hoy);
            array_push($array,$sql);

            $sql=db_compra_pago_insertar(1,$o->id,$localidadid,$o->cuentaid,$cta["nombre"],$o->monto,$usuarioid,$hoy);
            array_push($array,$sql);

            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="PAGO SERV.".$serv["proveedor_nombre"]." - ".$serv["producto_nombre"];
            $odet->monto=$o->monto*-1;
            $odet->saldo=$cta["saldo"]+$odet->monto;
            $odet->cuentaid= $o->cuentaid;
            $sql=$this->sqlInsert("cuenta_detalle",$odet); 
            array_push($array,$sql);

            $sql=$this->sqlUpdateSum("cuenta",$o->cuentaid,"saldo",$odet->monto);
            array_push($array,$sql);

            $sql=db_servicio_actualizar_ultimo_pago($o->servicioid,$o->monto,$usuarioid,$hoy);
            array_push($array,$sql);

            $this->db->transacm($array,"Se pagó un servicio correctamente");
        }
    }
?>