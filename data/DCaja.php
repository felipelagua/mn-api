<?php
    class DCaja extends Model{
        private $table="caja";

        public function aperturar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $o->id=guid();
            $o->saldo=0;
            $o->estado="A";
            $o->usuarioid=$usuarioid;
            $o->localidadid=$localidadid;
            
            if(!$this->existe()){
                $micuenta=$this->cuentaCaja();
                $saldoreserva=$this->obtenerSaldoReserva();

                $array = array();
                 if($saldoreserva>0){
                        $arrsaldo=$this->agregarReserva($micuenta["id"],$saldoreserva);
                        foreach($arrsaldo as $sqlsaldo){
                            array_push($array, $sqlsaldo);
                        }
                 }
                $o->saldo_inicial=$micuenta["saldo"]+$saldoreserva;
                $det=new EcajaDetalle(null);
                $det->id = guid();
                $det->cajaid=$o->id;
                $det->tipo="ING";
                $det->descripcion="SALDO INICIAL";
                $det->monto=$o->saldo_inicial;
                $det->saldo=$o->saldo_inicial;
    
                $sqlCrearcaja=$this->sqlInsert($this->table,$o);
                $sqlInsertarDetalle=$this->sqlInsert("caja_detalle",$det); 
                $sqlActualizarSaldo=$this->sqlUpdateSum($this->table,$o->id,"saldo",$det->monto);

                array_push($array, $sqlCrearcaja, $sqlInsertarDetalle, $sqlActualizarSaldo);
 
                $this->db->transac($array);
            }
            else{
                $message="Ya existe caja  aperturada en su localidad con su usuario";
                $this->gotoError($message);
            }
        }
    
        private function existe(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $state=false;
            $sql="select id from caja where usuarioid='$usuarioid' and localidadid='$localidadid' and estado in ('A','R') and activo=1";
            $dt=$this->sqldata($sql);
            if(count($dt)>0){
                $state=true;
            }
            return $state;
        }

        private function cuentaCaja(){
            $usuarioid=auth::user();
            $sql="SELECT a.id,a.saldo,a.nombre,a.cuentacierreid
            FROM cuenta AS a
            INNER JOIN formapago AS b ON b.id=a.formapagoid
            WHERE a.usuarioid='$usuarioid'
            AND b.caja='SI'
            AND a.activo=1
            AND b.activo=1;";
            $dt=$this->sqldata($sql);
            if(count($dt)==0){
                $message="No tienes una cuenta con forma de pago para caja";
                $this->gotoError($message);
            }
            return $dt[0];
        }

        private function obtenerSaldoReserva(){
            $localidadid=auth::local();
            $sql="SELECT ifnull(sum(a.saldo),0.00) AS saldo
            FROM caja_reserva AS a
            WHERE a.localidadid='$localidadid'
            AND a.estado='A' AND a.activo=1;";
            $dt=$this->sqldata($sql);
            return $dt[0]["saldo"];
        }

        public function agregarReserva($cuentaid,$monto){
            $localidadid=auth::local();
            $usuarioid=auth::user();
            $row=$this->get("cuenta",$cuentaid,["saldo"]);
            $saldo=$row["saldo"];

            $det= new ECuentaDetalle();
            $det->id=guid();
            $det->cuentaid = $cuentaid;
            $det->tipo = "ING";
            $det->descripcion ="TOMA DE RESERVA";
            $det->monto = $monto;
            $det->saldo=$saldo+$monto;

            $sqlInsertarDetalle=$this->sqlInsert("cuenta_detalle",$det); 
            $sqlActualizarSaldo=$this->sqlUpdateSum("cuenta",$cuentaid,"saldo",$monto);
            $sqlActualizarReserva="update caja_reserva set estado='C',usuario_modificacion='$usuarioid'
            where localidadid='$localidadid' and estado='A' and activo=1";
            $array = array($sqlInsertarDetalle,$sqlActualizarSaldo,$sqlActualizarReserva);
           // $this->db->transac($array);
           return $array;
        }  
 
        public function obtener($o){
            $sql="select a.id,a.nombre,b.nombre as usuario_nombre,a.usuarioid,a.formapagoid,
            c.nombre as formapago_nombre,a.saldo,a.saldo_inicial,a.venta
            from caja as a
            inner join usuario as b on b.id=a.usuarioid
            inner join formapago as c on c.id = a.formapagoid
            where a.id='$o->id' and a.activo=1";

            $sqlusuario="select id,nombre
            from usuario where activo=1 
            order by nombre";

            $sqlformapago="select id,nombre
            from formapago where activo=1 
            order by nombre";

            $data["ent"]=$this->sqlgetrow($sql);
            $data["personas"]=$this->sqldata($sqlusuario);
            $data["formapago"]=$this->sqldata($sqlformapago);
            $this->gotoSuccessData($data);
        }
        public function obtenerDatosApertura(){
          if($this->existe()){
            $message="La caja se encuentra activa";
            $dt = $this->obtenerCajaAbierta();
            if(count($dt)==0){
                $message="La caja se encuentra con reserva";
             }
             $this->gotoError($message);
          }

            $saldoreserva=$this->obtenerSaldoReserva();
              $row=$this->cuentaCaja();
            $row["estado_nombre"]="CAJA CERRADA";
            $row["estado"]="C";

            if($this->existe()){
                $row["estado_nombre"]="CAJA ABIERTA";
                $row["estado"]="A";
            }
 
            $row["saldo_reserva"]=$saldoreserva;
            $row["saldo_total"]=$row["saldo"]+$row["saldo_reserva"];
           
            $this->gotoSuccessData($row);
        }
        public function registrarMovimiento($o){
            $row=$this->get($this->table,$o->cajaid,["saldo"]);
            $saldo=$row["saldo"];
            

            if($o->tipo=="SAL"){
                $this->validateBalance($saldo,$o->monto);
                $o->monto=$o->monto*-1;    
            }
            $o->saldo=$saldo+$o->monto;

            $sqlInsertarDetalle=$this->sqlInsert("caja_detalle",$o); 
            $sqlActualizarSaldo=$this->sqlUpdateSum("caja",$o->cajaid,"saldo",$o->monto);
            $array = array($sqlInsertarDetalle,$sqlActualizarSaldo);
            $this->db->transac($array);
        }  

        public function obtenerDatosReserva(){
             $dt = $this->obtenerCajaAbierta();

             if(count($dt)>0){
                $row=$dt[0];
                $this->gotoSuccessData($row);
             }
             else{
                $message="La caja no se encuentra abierta";
                $this->gotoError($message);
             }
        }
        private function obtenerCajaAbierta(){
            $localidadid=auth::local();
            $usuarioid=auth::user();
            $sql="SELECT estado,saldo,0.00 as monto,a.id
            FROM caja AS a
            WHERE 
            a.usuarioid='$usuarioid'
            AND a.localidadid = '$localidadid'
            AND a.activo=1 and a.estado in  ('A','R')";
            $dt = $this->sqldata($sql);
            return $dt;
        }

        public function reservar($o){
            $localidadid=auth::local();
            $usuarioid=auth::user();
            $dt = $this->obtenerCajaAbierta();
            if(count($dt)==0){
                $message="La caja no se encuentra abierta";
                $this->gotoError($message);
             }
             $row=$dt[0];
             $cajaid=$row["id"];
             $saldo=$row["saldo"];
             if($o->monto>$saldo){
                $message="El monto ingresado es mayor al saldo";
                $this->gotoError($message);
             }
             $hoy=now();
            $odet=new ECajaDetalle(null);
            $odet->tipo="SAL";
            $odet->descripcion="RESERVA";
            $odet->monto=$o->monto*-1;
            $odet->saldo=$saldo+$odet->monto;
            $odet->cajaid=$cajaid;

            $sqlInsertarDetalleCaja=$this->sqlInsert("caja_detalle",$odet); 
            $sqlActualizarSaldoCaja=$this->sqlUpdateSum("caja",$cajaid,"saldo",$odet->monto);
            $reservaid=guid();
            $sqlInsertarCajaReserva="insert into caja_reserva(id,localidadid,saldo,estado,activo,usuario_creacion,fecha_hora_creacion)
            values('$reservaid','$localidadid','$o->monto','A',1,'$usuarioid',$hoy)";
            $sqlCambiarEstadoCaja="update caja set estado='R',fecha_hora_modificacion= $hoy where id='$cajaid'";
            $cuenta=$this->cuentaCaja();
            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="TRANSFERENCIA POR CIERRE ";
            $odet->monto=$o->monto*-1;
            $odet->saldo=$cuenta["saldo"]+$odet->monto;
            $odet->cuentaid= $cuenta["id"];
            $sqlInsertarDetalleCuenta=$this->sqlInsert("cuenta_detalle",$odet); 
            $sqlActualizarSaldoCuenta=$this->sqlUpdateSum("cuenta",$cuenta["id"],"saldo",$odet->monto);
            $array = array($sqlInsertarDetalleCaja,
            $sqlActualizarSaldoCaja,
            $sqlInsertarCajaReserva,
            $sqlCambiarEstadoCaja,
            $sqlInsertarDetalleCuenta,
            $sqlActualizarSaldoCuenta);
            $this->db->transacm($array,"Se reservó efectivo con éxito");
        } 

    
        public function obtenerDetalle(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=  $sql="SELECT a.id,a.saldo,b.nombre as localidad_nombre,date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_ini,
            date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_fin
            FROM caja AS a
            inner join localidad as b on b.id=a.localidadid
            WHERE a.usuarioid='$usuarioid'
            and a.localidadid='$localidadid' and a.estado in  ('A','R') and a.activo=1";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $cajaid=$data["cabecera"]["id"];
            $sqldet="SELECT 
            DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
            a.descripcion,a.monto,a.saldo,a.tipo
            FROM caja_detalle AS a
            WHERE a.cajaid='$cajaid'
            and a.activo=1
            ORDER BY a.fecha_hora_creacion DESC
            LIMIT 0,100";

           
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
        public function obtenerPreCierre(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=  $sql="SELECT a.id,a.saldo,b.nombre as localidad_nombre,date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_ini,
            date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_fin
            FROM caja AS a
            inner join localidad as b on b.id=a.localidadid
            WHERE a.usuarioid='$usuarioid'
            and a.localidadid='$localidadid' and a.estado in  ('A','R') and a.activo=1";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $cajaid=$data["cabecera"]["id"];
            $sqldet="SELECT UUID() AS id,'SALDO INICIAL' AS descripcion,1 AS cantidad, a.saldo_inicial AS monto 
            FROM caja AS a
            WHERE a.id='$cajaid' AND a.activo=1
            UNION ALL
            SELECT UUID() AS id,'INGRESO EFECTIVO',COUNT(a.id) AS cantidad,SUM(a.monto) AS monto
            FROM caja_detalle AS a
            WHERE a.cajaid='$cajaid' AND a.activo=1 AND a.tipo='ING'
            UNION ALL
            SELECT UUID() AS id,'SALIDA EFECTIVO',COUNT(a.id) AS cantidad,SUM(a.monto) AS monto
            FROM caja_detalle AS a
            WHERE a.cajaid='$cajaid' AND a.activo=1 AND a.tipo='SAL'
            UNION ALL
            SELECT UUID() AS id,'SALDO CAJA' AS nombre,1 AS cantidad, a.saldo AS monto 
            FROM caja AS a
            WHERE a.id='$cajaid' AND a.activo=1";

           
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
        public function finalizar(){
            $dt = $this->obtenerCajaAbierta();
            if(count($dt)==0){
                $message="La caja no se encuentra abierta";
                $this->gotoError($message);
             }
             $row=$dt[0];
             $cajaid=$row["id"];
             $monto=$row["saldo"];
             $hoy=now();
             $sqlCierreCaja=" insert into caja_cierre(id,cajaid,descripcion,cantidad,monto,activo)
             SELECT UUID() AS id,'$cajaid' as cajaid,'SALDO INICIAL' AS descripcion,1 AS cantidad, a.saldo_inicial AS monto,1
             FROM caja AS a
             WHERE a.id='$cajaid' AND a.activo=1
             UNION ALL
             SELECT UUID() AS id,'$cajaid' as cajaid,'INGRESO EFECTIVO',COUNT(a.id) AS cantidad,SUM(a.monto) AS monto,1
             FROM caja_detalle AS a
             WHERE a.cajaid='$cajaid' AND a.activo=1 AND a.tipo='ING'
             UNION ALL
             SELECT UUID() AS id,'$cajaid' as cajaid,'SALIDA EFECTIVO',COUNT(a.id) AS cantidad,SUM(a.monto) AS monto,1
             FROM caja_detalle AS a
             WHERE a.cajaid='$cajaid' AND a.activo=1 AND a.tipo='SAL'
             UNION ALL
             SELECT UUID() AS id,'$cajaid' as cajaid,'SALDO CAJA' AS nombre,1 AS cantidad, a.saldo AS monto ,1
             FROM caja AS a
             WHERE a.id='$cajaid' AND a.activo=1";

             $sqlCambiarEstadoCaja="update caja set estado='C',fecha_hora_modificacion= $hoy where id='$cajaid'";

            $cuenta=$this->cuentaCaja();
           
            $cuentaorigenid=$cuenta["id"];
            $cuentadestinoid=$cuenta["cuentacierreid"];

            if($cuentadestinoid==null || $cuentadestinoid==""){
                $message="No tiene una cuanta de cierre destino";
                $this->gotoError($message);
            }
            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="TRANSFERENCIA POR CIERRE ";
            $odet->monto=$monto*-1;
            $odet->saldo=$cuenta["saldo"]+$odet->monto;
            $odet->cuentaid=$cuentaorigenid;
            $sqlInsertarDetalleOrigen=$this->sqlInsert("cuenta_detalle",$odet); 
            $sqlActualizarSaldoOrigen=$this->sqlUpdateSum("cuenta",$cuentaorigenid,"saldo",$odet->monto);
           
            $saldoDestino=$this->get("cuenta",$cuentadestinoid,["saldo"])["saldo"];
           
            $ddet=new ECuentaDetalle();
            $ddet->tipo="ING";
            $ddet->descripcion="TRANSFERENCIA";
            $ddet->monto=$monto;
            $ddet->saldo=$saldoDestino+$ddet->monto;
            $ddet->cuentaid=$cuentadestinoid;
            $sqlInsertarDetalleDestino=$this->sqlInsert("cuenta_detalle",$ddet); 
            $sqlActualizarSaldoDestino=$this->sqlUpdateSum("cuenta",$cuentadestinoid,"saldo",$ddet->monto);

            $array = array($sqlCierreCaja,
                $sqlCambiarEstadoCaja, 
                $sqlInsertarDetalleOrigen,
                $sqlActualizarSaldoOrigen,
                $sqlInsertarDetalleDestino,
                $sqlActualizarSaldoDestino
            );
            $this->db->transacm($array,"Se cerró caja con éxito");
        } 

    }
?>