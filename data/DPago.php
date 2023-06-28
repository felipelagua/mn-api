<?php
usingdb("pago");
usingdb("cuenta");
usingdb("localidad");
usingdb("trabajador");
    class DPago extends Model{
 
        public function listarPeriodo($o){         
            $sql=db_pago_periodo_listar($o->anio,$o->mes);
            return $this->sqldata($sql);
        }
        public function obtenerFiltros(){
            $hoy=now();
            
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format($hoy,'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM pago";
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
 
            $data["ent"]=$this->sqlrow(db_trabajador_obtener($o->id));
            $data["cuenta"]=$this->sqldata(db_cuenta_pago_listar());
          
            $this->gotoSuccessData($data); 
 
        }
        public function registrarPago($o){
            $hoy=now();$usuarioid=auth::user(); 
            $this->validar($o);
            $tra=$this->sqlrow(db_trabajador_obtener($o->trabajadorid));
            $cta=$this->sqlrow(db_cuenta_obtener($o->cuentaid));

            $o->id=guid();
            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="PAGO";
            $odet->monto=$tra["sueldo"]*-1;
            $odet->saldo=$cta["saldo"]+$odet->monto;
            $odet->cuentaid=$o->cuentaid;

            $array = array();

            $sql=$this->sqlInsert("cuenta_detalle",$odet);
            array_push($array,$sql);
            $sql=$this->sqlUpdateSum("cuenta",$o->cuentaid,"saldo",$odet->monto);
            array_push($array,$sql);
            $sql=db_pago_insertar($o->id,$tra["localidadid"],$o->trabajadorid,$o->anio,$o->mes,$o->cuentaid,$tra["sueldo"],$usuarioid,$hoy);
            array_push($array,$sql);

            $this->db->transacm($array,"Se registró un pago correctamente");
            
        }

        private function validar($o){
            $dttra=$this->sqldata(db_trabajador_obtener($o->trabajadorid));
            $dtcta=$this->sqldata(db_cuenta_obtener($o->cuentaid));
            $details = array();

            if(count($dttra)==0){
                array_push($details,"El trabajador no es válido");
            }
            if(count($dtcta)==0){
                array_push($details,"La cuenta no es válida");
            }
            if(count($dttra)>0 && count($dtcta)>0){
                $tra=$dttra[0];$cta=$dtcta[0];
                if($tra["sueldo"]>$cta["saldo"]){
                    array_push($details,"El pago del trabajador es mayor al saldo de la cuenta");
                }

                if($o->anio<2020){
                    array_push($details,"El año no es válido");
                }
            }

            if(count($details)){
                $this->gotoErrorDetails("Ocurrieron algunos errores",$details); 
            }
        }
    }
?>