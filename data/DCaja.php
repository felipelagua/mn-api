<?php
usingdb("localidad");
usingdb("caja");
    class DCaja extends Model{
        private $table="caja";
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id,date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,a.numero,
            b.nombre AS localidad_nombre,
            c.nombre AS usuario_nombre,a.saldo_inicial, a.saldo,
            case when a.estado='A'  then 'ABIERTO' ELSE 'CERRADO' END AS estado
            FROM caja AS a
            INNER JOIN localidad AS b ON b.id=a.localidadid
            INNER JOIN usuario AS c ON c.id=a.usuarioid
             WHERE a.activo=1";
          
            if($o->tipo=="M"){
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y') = '$o->anio'
                and date_format(a.fecha_hora_creacion,'%m') = '$o->mes'";
            }
            else{
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y-%m-%d') between  '$o->desde' and  '$o->hasta'";
            }
            $sql.=" and ('$o->usuariocreador'='X' or a.usuario_creacion='$o->usuariocreador')";
            $sql.=" and ('$o->nombre'='' or a.numero='$o->nombre')";
            $sql.=" order by a.fecha_hora_creacion desc";
             
           
            $this->sqlread($sql);
             
        }
        public function aperturar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $this->validarLocal();
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

                if($det->saldo<=0){
                   // $this->gotoError("No puede aperturar caja con saldo cero");
                }
    
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
        private function validarLocal(){
            $localidadid=auth::local();
            $sql="SELECT a.id,ifnull(a.venta,'') as venta
            FROM localidad AS a
            WHERE a.id='$localidadid'
            AND a.activo=1 ";
            $dt=$this->sqldata($sql);
            if(count($dt)==0){
                $message="El local no es válido";
                $this->gotoError($message);
            }
            else{
                $row=$dt[0];
                if($row["venta"]!="SI"){
                    $message="El local no es un Punto de Venta";
                    $this->gotoError($message);
                }
            }
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
            $this->validarLocal();
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
            $this->validarLocal();
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
            $this->validarLocal();
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
            $this->validarLocal();
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
             if($o->monto<=0){
                $message="El monto a reservar debe ser mayor a cero";
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
            $odet->descripcion="RESERVA CAJA";
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
            $this->validarLocal();
            $dt= $this->obtenerCajaAbierta();
             if(count($dt)==0){
                $message="La caja no se encuentra abierta";
                $this->gotoError($message);
             }
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
    
           
            $data["detalle"]=$this->sqldata(db_caja_detalle_listar($cajaid));
            $this->gotoSuccessData($data);
        }
        public function obtenerDetalleCierre($o){
            $data["cabecera"]=$this->sqlrow(db_caja_obtener($o->id));
            $data["detalle"]=$this->sqldata(db_caja_cierre_listar($o->id));
            $this->gotoSuccessData($data);
        }
        public function obtenerPreCierre(){
            $dt= $this->obtenerCajaAbierta();
             if(count($dt)==0){
                $message="La caja no se encuentra abierta";
                $this->gotoError($message);
             }
            $this->validarLocal();
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $hoy=now();
 
            $data["cabecera"]=$this->sqlgetrow(db_caja_pre_cierre_obtener($localidadid,$usuarioid,$hoy));
            $cajaid=$data["cabecera"]["id"];
           
            $data["detalle"]=$this->sqldata(db_caja_pre_cierre_detalle($cajaid));
            $data["impresora"]=$this->sqlrow(db_localidad_impresora_obtener($localidadid));
            $data["movimiento"]=$this->sqldata(db_caja_detalle_listar($cajaid));
           return $data;
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
             WHERE a.id='$cajaid' AND a.activo=1
             union all 
            select uuid(),'$cajaid' as cajaid,concat('VENTA ',e.nombre),count(a.cuentaid) as cantidad,sum(a.pago) as monto,1
            from venta_pago as a 
            inner join venta as b on b.id=a.ventaid
            inner join pedido as c on c.id=b.pedidoid
            inner join cuenta as d on d.id=a.cuentaid
            inner join formapago as e on e.id=d.formapagoid  
            where b.cajaid='$cajaid'
            group by e.nombre
            UNION ALL
            select uuid(),'$cajaid' as cajaid,'TOTAL VENTA',count(a.cuentaid) as cantidad,sum(a.pago) as monto ,1
            from venta_pago as a 
            inner join venta as b on b.id=a.ventaid
            where b.cajaid='$cajaid'
             
             ";

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
        public function obtenerFiltros(){
            $hoy=now();
            
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format($hoy,'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM caja";
            $sqlusuario="SELECT id,nombre FROM usuario WHERE id IN (SELECT usuario_creacion FROM caja WHERE activo=1) AND activo=1";
            $sqlanioactual="SELECT date_format($hoy,'%Y') AS id,date_format($hoy,'%Y') as nombre";

            $anios=$this->sqldata($sqlanio);
            $anioactual=$this->sqlgetrow($sqlanioactual);
            if(count($anios)==0){
                $anios[0]=$anioactual;
            }
         
            $data["usuarios"]=$this->sqldata($sqlusuario);
            $data["meses"]=$this->sqldata($sqlmes);
            $data["anios"]=$anios;
            $data["mesactual"]=$this->sqlgetrow($sqlmesactual)["mes"] ;
            $data["anioactual"]= $anioactual["id"] ;
            $data["personas"]=$this->sqldata($sqlusuario);
            $data["localidad"]=$this->sqldata(db_localidad_listar());
            $this->gotoSuccessData($data); 
            
        }

    }
?>