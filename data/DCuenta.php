<?php
usingdb("cuenta");
    class DCuenta extends Model{
        private $table="cuenta";

        public function registrar($o){
            if(!$this->existe($o)){
                $det=new ECuentaDetalle();
                $det->id = guid();
                $det->cuentaid=$o->id;
                $det->tipo="ING";
                $det->descripcion="SALDO INICIAL";
                $det->monto=$o->saldo_inicial;
                $det->saldo=$o->saldo_inicial;
    
                if($o->cuentacierreid=="X"){
                    $o->cuentacierreid="";
                }
                $sqlCrearCuenta=$this->sqlInsert($this->table,$o);
                $sqlInsertarDetalle=$this->sqlInsert("cuenta_detalle",$det); 
                $sqlActualizarSaldo=$this->sqlUpdateSum($this->table,$o->id,"saldo",$det->monto);

                $array = array($sqlCrearCuenta, $sqlInsertarDetalle, $sqlActualizarSaldo);
                $this->db->transac($array);
            }
            else{
                $this->actualizar($o);
            }
        }
        private function actualizar($o){
            $hoy=now();
            if($o->cuentacierreid=="X"){
                $o->cuentacierreid="";
            }
            $sql="update cuenta set 
            nombre='$o->nombre',
            venta='$o->venta',
            pago='$o->pago',
            usuarioid='$o->usuarioid',
            formapagoid='$o->formapagoid',
            cuentacierreid='$o->cuentacierreid',
            fecha_hora_modificacion=$hoy
            where id='$o->id'";
            $this->db->execute($sql);
            $this->gotoSuccess("Se actualizó la cuenta con éxito",$o->id);
        }
        private function existe($o){
            $state=false;
            $sql="select id from cuenta where id='$o->id' and activo=1";
            $dt=$this->sqldata($sql);
            if(count($dt)>0){
                $state=true;
            }
            return $state;
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
           $sql="select a.id,a.nombre,b.nombre as usuario_nombre,a.venta,
           c.nombre as formapago_nombre,a.saldo,date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_hora
           from cuenta as a
           inner join usuario as b on b.id=a.usuarioid
           inner join formapago as c on c.id = a.formapagoid
           where a.activo=1
           and ('$o->nombre'='' or a.nombre like concat('%','$o->nombre','%'))";
           $this->sqlread($sql);
        }
        public function obtener($o){
     
            $sqlusuario="select id,nombre
            from usuario where activo=1 
            order by nombre";

            $sqlformapago="select id,nombre
            from formapago where activo=1 
            order by nombre";

            $sqlcuenta="select id,nombre
            from cuenta where activo=1 
            order by nombre";

            $data["ent"]=$this->sqlgetrow(db_cuenta_obtener($o->id));
            $data["personas"]=$this->sqldata($sqlusuario);
            $data["formapago"]=$this->sqldata($sqlformapago);
            $data["cuentas"]=$this->sqldata($sqlcuenta);
            $this->gotoSuccessData($data);
        }
        public function registrarMovimiento($o){
            $row=$this->get($this->table,$o->cuentaid,["saldo"]);
            $saldo=$row["saldo"];
            

            if($o->tipo=="SAL"){
                $this->validateBalance($saldo,$o->monto);
                $o->monto=$o->monto*-1;    
            }
            $o->saldo=$saldo+$o->monto;

            $sqlInsertarDetalle=$this->sqlInsert("cuenta_detalle",$o); 
            $sqlActualizarSaldo=$this->sqlUpdateSum("cuenta",$o->cuentaid,"saldo",$o->monto);
            $array = array($sqlInsertarDetalle,$sqlActualizarSaldo);
            $this->db->transac($array);
        }  

        public function transferir($o){
            $saldoOrigen=$this->get($this->table,$o->cuentaorigenid,["saldo"])["saldo"];
            
            $this->validateBalance($saldoOrigen,$o->monto);

            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="TRANSFERENCIA";
            $odet->monto=$o->monto*-1;
            $odet->saldo=$saldoOrigen+$odet->monto;
            $odet->cuentaid=$o->cuentaorigenid;

            $sqlInsertarDetalleOrigen=$this->sqlInsert("cuenta_detalle",$odet); 
            $sqlActualizarSaldoOrigen=$this->sqlUpdateSum("cuenta",$o->cuentaorigenid,"saldo",$odet->monto);
             
            $saldoDestino=$this->get($this->table,$o->cuentadestinoid,["saldo"])["saldo"];

            $ddet=new ECuentaDetalle();
            $ddet->tipo="ING";
            $ddet->descripcion="TRANSFERENCIA";
            $ddet->monto=$o->monto;
            $ddet->saldo=$saldoDestino+$ddet->monto;
            $ddet->cuentaid=$o->cuentadestinoid;

            $sqlInsertarDetalleDestino=$this->sqlInsert("cuenta_detalle",$ddet); 
            $sqlActualizarSaldoDestino=$this->sqlUpdateSum("cuenta",$o->cuentadestinoid,"saldo",$o->monto);

            $array = array($sqlInsertarDetalleOrigen,$sqlActualizarSaldoOrigen,$sqlInsertarDetalleDestino,$sqlActualizarSaldoDestino);
            $this->db->transac($array);
        } 

        public function obtenerListas(){

            $sqlusuario="select id,nombre
            from usuario where activo=1 
            order by nombre";

            $sqlformapago="select id,nombre
            from formapago where activo=1 
            order by nombre";

            $sqlcuenta="select id,nombre
            from cuenta where activo=1 
            order by nombre";

            $data["personas"]=$this->sqldata($sqlusuario);
            $data["formapago"]=$this->sqldata($sqlformapago);
            $data["cuentas"]=$this->sqldata($sqlcuenta);
            $this->gotoSuccessData($data);
        }
        public function obtenerListasTransferir(){

            $sqlcuenta="SELECT a.id,concat(a.nombre,' = S/ ',a.saldo) AS nombre
            FROM cuenta AS a
            WHERE a.activo=1
            ORDER BY a.nombre";

            $data["origen"]=$this->sqldata($sqlcuenta);
            $data["destino"]=$this->sqldata($sqlcuenta);
            $this->gotoSuccessData($data);
        }

        public function obtenerDetalle($o){
            $sql=  $sql="SELECT a.id,a.nombre,saldo
            FROM cuenta AS a
            WHERE a.id =  '$o->id' and a.activo=1";

            $sqldet="SELECT 
            DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
            a.descripcion,a.monto,a.saldo,a.tipo
            FROM cuenta_detalle AS a
            WHERE a.cuentaid='$o->id'
            and a.activo=1
            ORDER BY a.fecha_hora_creacion DESC
            LIMIT 0,100";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
        public function obtenerListasMov(){

            $sqlcuenta="SELECT a.id,concat(a.nombre,' = S/ ',a.saldo) AS nombre
            FROM cuenta AS a
            WHERE a.activo=1
            ORDER BY a.nombre";

            $sqltipo="SELECT 'ING' AS id,'INGRESO' AS nombre
            UNION ALL
            SELECT 'SAL' AS id,'SALIDA' AS nombre";

            $data["cuenta"]=$this->sqldata($sqlcuenta);
            $data["tipo"]=$this->sqldata($sqltipo);
            $this->gotoSuccessData($data);
        }
    }
?>