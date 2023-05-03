<?php
    class DMicuenta extends Model{
        private $table="cuenta";
 
  
        public function listar($o){
            $usuarioid=auth::user();
            //$localidadid=auth::local();

           $sql="select a.id,a.nombre,b.nombre as usuario_nombre,a.venta,
           c.nombre as formapago_nombre,a.saldo,date_format(a.fecha_hora_modificacion,'%d/%m/%Y %H:%i') as fecha_hora
           from cuenta as a
           inner join usuario as b on b.id=a.usuarioid
           inner join formapago as c on c.id = a.formapagoid
           where a.usuarioid='$usuarioid'
           and ('$o->nombre'='' or a.nombre like concat('%','$o->nombre','%'))
           and a.activo=1";
           $this->sqlread($sql);
        }
        public function obtener($o){
            $this->validarCuenta($o);
            $sql="select a.id,a.nombre,b.nombre as usuario_nombre,a.usuarioid,a.formapagoid,
            c.nombre as formapago_nombre,a.saldo,a.saldo_inicial,a.venta,
            case when a.cuentacierreid=null || a.cuentacierreid='' then 'X' else a.cuentacierreid end as cuentacierreid,
            ifnull(d.nombre,'') as cuentadestino_nombre
            from cuenta as a
            inner join usuario as b on b.id=a.usuarioid
            inner join formapago as c on c.id = a.formapagoid
            left join cuenta as d on d.id=a.cuentacierreid
            where a.id='$o->id' and a.activo=1";
            $data["ent"]=$this->sqlgetrow($sql);
          
            $this->gotoSuccessData($data);
        }
        private function validarCuenta($o){
            $usuarioid=auth::user();
            $sql="select id,cuentacierreid,saldo
            from cuenta
            where id='$o->id' and usuarioid='$usuarioid' and activo=1";
            $dt=$this->sqldata($sql);
            if(count($dt)==0){
                $message="La cuenta no es válida";
                $this->gotoError($message);
            }
        }
        private function obtenerCuenta($o){
            $usuarioid=auth::user();
            $sql="select id,cuentacierreid,saldo
            from cuenta
            where id='$o->id' and usuarioid='$usuarioid' and activo=1";
            $dt=$this->sqldata($sql);
            if(count($dt)==0){
                $message="La cuenta no es válida";
                $this->gotoError($message);
            }
            else{
                $row=$dt[0];
                if($row["cuentacierreid"]==""){
                    $message="La cuenta no tiene una cuenta destino";
                    $this->gotoError($message);
                }
                else{
                    if($row["saldo"]>0){
                        return $row;
                    }
                    else{
                        $message="La cuenta no tiene saldo";
                        $this->gotoError($message);
                    }    
                }
            }
        }

        public function devolver($o){
            $this->validarCajaAbierta();
            $cuenta=$this->obtenerCuenta($o);
            $saldoOrigen=$this->get($this->table,$o->id,["saldo"])["saldo"];
            
            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="DEVOLUCION DE SALDO";
            $odet->monto=$cuenta["saldo"]*-1;
            $odet->saldo=$saldoOrigen+$odet->monto;
            $odet->cuentaid=$o->id;

            $sqlInsertarDetalleOrigen=$this->sqlInsert("cuenta_detalle",$odet); 
            $sqlActualizarSaldoOrigen=$this->sqlUpdateSum("cuenta",$o->id,"saldo",$odet->monto);
             
            $saldoDestino=$this->get($this->table,$cuenta["cuentacierreid"],["saldo"])["saldo"];

            $ddet=new ECuentaDetalle();
            $ddet->tipo="ING";
            $ddet->descripcion="DEVOLUCION DE SALDO";
            $ddet->monto=$cuenta["saldo"];
            $ddet->saldo=$saldoDestino+$ddet->monto;
            $ddet->cuentaid= $cuenta["cuentacierreid"];

            $sqlInsertarDetalleDestino=$this->sqlInsert("cuenta_detalle",$ddet); 
            $sqlActualizarSaldoDestino=$this->sqlUpdateSum("cuenta",  $ddet->cuentaid ,"saldo",$ddet->monto);

            $array = array($sqlInsertarDetalleOrigen,$sqlActualizarSaldoOrigen,$sqlInsertarDetalleDestino,$sqlActualizarSaldoDestino);
            $this->db->transacm($array,"Se realizó la operación con éxito");
        } 

        public function obtenerDetalle($o){
            $this->validarCuenta($o);
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
        private function validarCajaAbierta(){
            $localidadid=auth::local();
            $usuarioid=auth::user();
            $sql="SELECT estado,saldo,0.00 as monto,a.id
            FROM caja AS a
            WHERE 
            a.usuarioid='$usuarioid'
            AND a.localidadid = '$localidadid'
            AND a.activo=1 and a.estado in  ('A','R')";
            $dt = $this->sqldata($sql);
            if(count($dt)>0){
                $message="La caja se encuentra abierta, al cerrar caja se retornará automaticamente a la cuenta destino";
                $this->gotoError($message);
            }
        } 
    }
?>