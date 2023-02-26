<?php
    class DCuenta extends Model{
        private $table="cuenta";

        public function crear($o){
            $det=new ECuentaDetalle();
            $det->id = guid();
            $det->cuentaid=$o->id;
            $det->tipo="ING";
            $det->descripcion="SALDO INICIAL";
            $det->monto=$o->saldo_inicial;
            $det->saldo=$o->saldo_inicial;
 
             $sqlCrearCuenta=$this->sqlInsert($this->table,$o);
             $sqlInsertarDetalle=$this->sqlInsert("cuenta_detalle",$det); 
             $sqlActualizarSaldo=$this->sqlUpdateSum($this->table,$o->id,"saldo",$det->monto);

             $array = array($sqlCrearCuenta, $sqlInsertarDetalle, $sqlActualizarSaldo);
             $this->db->transac($array);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar(){
            $ent=new ECuentaDto(null);
            $this->all($this->table,$ent);
        }
        public function obtener($o){
            $row=$this->get("cuenta",$o->id,[]);
            $this->gotoSuccessData($row);
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
    }
?>