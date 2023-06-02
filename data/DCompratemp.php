<?php
    class DCompratemp extends Model{
        private $table="compratemp";
        private function validarCajaAbierta(){
            $localidadid=auth::local();
            $usuarioid=auth::user();
            $sql="SELECT estado,saldo,0.00 as monto,a.id
            FROM caja AS a
            WHERE 
            a.usuarioid='$usuarioid'
            AND a.localidadid = '$localidadid'
            AND a.activo=1 and a.estado in  ('A')";
            $dt = $this->sqldata($sql);
            if(count($dt)==0){
                $message="Debe aperturar Caja";
                $this->gotoError($message);
            }
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
                if($row["venta"]=="SI"){
                   $this->validarCajaAbierta();
                }
            }
        }
        public function obtener(){
            $this->validarLocal();
            $usuarioid=auth::user();
            $localidadid=auth::local();
   
            $sql=" select a.id,a.tipocomprobanteid,a.proveedorid,a.numero,a.total,a.pago,a.saldo,
            ifnull(b.nombre,'') as proveedor_nombre,ifnull(c.nombre_corto,'') as tipocomprobante_nombre
             from ".$this->table." as a
             left join proveedor as b on b.id=a.proveedorid
             left join tipocomprobante as c on c.id=a.tipocomprobanteid
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";

             $sqldet=" select id,productoid,descripcion,cantidad,precio,importe
             from ".$this->table."_detalle
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";
            
             $sqltipocomprobante="select id,nombre,nombre_corto from tipocomprobante where activo=1 order by nombre";

          
             $sqlpago=" select id,cuentaid,descripcion,monto,pago,vuelto
             from ".$this->table."_pago
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";

             $cab= new ECompratemp($this->sqlgetrow($sql));
             if($cab->tipocomprobanteid==""){ $cab->tipocomprobanteid="X";}
             if($cab->total==""){ $cab->total="0.00";}
             if($cab->pago==""){ $cab->pago="0.00";}
             if($cab->saldo==""){ $cab->saldo="0.00";}
            $data["cabecera"]=$cab;
            $data["detalle"]=$this->sqldata($sqldet);
            $data["pago"]=$this->sqldata($sqlpago);
            $data["tipocomprobante"]=$this->sqldata($sqltipocomprobante);
            
            $this->gotoSuccessData($data); 
        }

        function registrar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            $hoy=now();
            $sqltable="";
             if(!$this->existe()){
                $sqltable="insert into compratemp(id,localidadid,proveedorid,tipocomprobanteid,numero,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->proveedorid','$o->tipocomprobanteid','$o->numero',0.00,0.00,0.00,1,'$usuarioid',".$hoy.")";
             }
             else{
                $sqltable="update compratemp
                set tipocomprobanteid='$o->tipocomprobanteid',
                proveedorid = '$o->proveedorid',
                numero='$o->numero'
                where localidadid='$localidadid'   and usuario_creacion='$usuarioid'";
             }
            $this->db->execute($sqltable);
            $this->gotoSuccess("Se grabaron los datos con éxito",$o->id);

        }
        
        function existe(){
            $state=false;
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=" select id from ".$this->table." 
            where localidadid='$localidadid' 
            and usuario_creacion='$usuarioid' ";
            $dt=$this->sqldata($sql);
            if(count($dt)>0){
                $state=true;
            }
            return $state;
        }
        public function buscarProveedor($o){
            $sql=" select id ,nombre
            from proveedor 
             where activo=1 
             and nombre like  '%".$o->nombre."%'
             order by nombre";
              $this->sqlread($sql);
        }
        public function buscarProducto($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=" select id as productoid,nombre as descripcion,1 as cantidad ,precio_compra as precio
            from producto 
            where activo=1 
            and terminado='NO'
            and instantaneo='NO'
            and compra='SI'
            and not id in (select productoid from ".$this->table."_detalle
            where localidadid='$localidadid' and usuario_creacion='$usuarioid')
            and nombre like  '%".$o->nombre."%'
            order by fecha_hora_creacion desc";
            $this->sqlread($sql);
        }
        public function listarDetalle(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sqldet=" select id,productoid,descripcion,cantidad,precio,importe
             from ".$this->table."_detalle
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1
             order by fecha_hora_creacion desc";

            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function registrarDetalle($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $hoy=now();

            $o->importe = $o->cantidad*$o->precio;

            $sql="";
            if(!$this->existeDetalle($o)){
                $o->id=Guid();
                $sql="insert into ".$this->table."_detalle(id,localidadid,productoid,descripcion,cantidad,precio,importe,'NO',activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->productoid','$o->descripcion','$o->cantidad','$o->precio','$o->importe',1,'$usuarioid',$hoy)";
            }
            else{
                $sql="update  ".$this->table."_detalle
                set cantidad = '$o->cantidad', 
                precio = '$o->precio', 
                importe = '$o->importe', 
                fecha_hora_modificacion=$hoy , usuario_modificacion='$usuarioid'
                where localidadid = '$localidadid'  and productoid='$o->productoid' and usuario_creacion='$usuarioid'"; 
            }
            $this->db->execute($sql);
            $this->actualizarTotal();
            $this->obtenerCabecera();
           
        }
        private function actualizarTotal(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql="update compratemp
            set total=(select ifnull(sum(importe),0) from ".$this->table."_detalle where localidadid='$localidadid' and usuario_creacion='$usuarioid' and activo=1),
            pago=(select ifnull(sum(pago),0) from ".$this->table."_pago where localidadid='$localidadid' and usuario_creacion='$usuarioid' and activo=1),
            saldo=(select ifnull(sum(importe),0) from ".$this->table."_detalle where localidadid='$localidadid' and usuario_creacion='$usuarioid' 
            and activo=1)-(select ifnull(sum(pago),0) from ".$this->table."_pago where localidadid='$localidadid' and usuario_creacion='$usuarioid' and activo=1)
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' 
            and activo=1";
            $this->db->execute($sql);
        }
        function obtenerCabecera(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=" select a.id,a.tipocomprobanteid,a.proveedorid,a.numero,a.total,a.pago,a.saldo,
            ifnull(b.nombre,'') as proveedor_nombre
             from ".$this->table." as a
             left join proveedor as b on b.id=a.proveedorid
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";
             $dt=$this->sqldata($sql);

             if(count($dt)>0){
                $row= $dt[0];
                $this->gotoSuccessData($row);
             }
             else{
                $this->gotoSuccessData("{}");
             }
            
        }
        function existeDetalle($o){
            $state=false;
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=" select id from ".$this->table."_detalle
            where localidadid='$localidadid' and productoid='$o->productoid'
            and usuario_creacion='$usuarioid' ";
            $dt=$this->sqldata($sql);
            if(count($dt)>0){
                $state=true;
            }
            return $state;
        }
        public function eliminarDetalle($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sqldet=" delete
             from ".$this->table."_detalle
             where localidadid='$localidadid'
             and productoid='$o->productoid' 
             and usuario_creacion='$usuarioid' ";

             $this->db->execute($sqldet);
             $this->actualizarTotal();
             $this->obtenerCabecera();
        }

        public function listarPago(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sqldet=" select id,cuentaid,descripcion,monto,pago,vuelto
             from ".$this->table."_pago
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1
             order by fecha_hora_creacion desc";

            $data["pago"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function listarPagoCuenta(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sqldet="select id,concat(nombre,' S/ ',saldo) as nombre
            from cuenta where usuarioid='$usuarioid' and activo=1 
            and not id in (select cuentaid from compratemp_pago 
            where usuarioid='$usuarioid' and localidadid='$localidadid' and activo=1)
            order by nombre";

            $data["cuenta"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function eliminarPago($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sqldet=" delete
             from ".$this->table."_pago
             where localidadid='$localidadid'
             and id='$o->id' 
             and usuario_creacion='$usuarioid' ";

             $this->db->execute($sqldet);
             $this->actualizarTotal();
             $this->obtenerCabecera();
        }
        public function registrarPago($o){
            $this->validarLocal();
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $hoy=now();
            $o->id=Guid();
            $cuenta=$this->validarCuenta($o);
            $o->descripcion=$cuenta["nombre"];

            $sql="insert into ".$this->table."_pago(id,localidadid,cuentaid,descripcion,pago,activo,usuario_creacion,fecha_hora_creacion)
            values('$o->id','$localidadid','$o->cuentaid','$o->descripcion','$o->pago',1,'$usuarioid',$hoy)";

            $this->db->execute($sql);
            $this->actualizarTotal();
            $this->obtenerCabecera();
           
        }
        private function validarCuenta($o){
            $usuarioid=auth::user();
            $sql="select id,nombre,saldo
            from cuenta
            where usuarioid='$usuarioid'
            and id='$o->cuentaid'
            and activo=1";
            $dt=$this->sqldata($sql);
            if(count($dt)==0){
                $message="La cuenta no es válida";
                $this->gotoError($message);
            }
            $row=$dt[0];
            if($o->pago>$row["saldo"]){
                $message="El pago es mayor al saldo de la cuenta seleccionada";
                $this->gotoError($message);
            }
            return $row;
        }
        function finalizar(){
            $this->validarLocal();
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            $hoy=now();
            $sql=" select a.id,a.proveedorid,a.tipocomprobanteid,ifnull(b.nombre_corto,'') as tipocomprobante_nombre,
             a.numero,a.total,a.pago,a.saldo,ifnull(c.nombre,'') as proveedor_nombre,c.credito
             from ".$this->table." as a 
             left join tipocomprobante as b on b.id=a.tipocomprobanteid
             left join proveedor as c on c.id=a.proveedorid
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";

             $sqldet=" select a.id,a.productoid,a.descripcion,a.cantidad,a.precio,a.importe,
             case when c.id is null then 'N' else 'S' end as locprod,
             case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
             b.stock,ifnull(c.precio,0.00) as precio_stock
             from ".$this->table."_detalle as a
             inner join producto as b on b.id=a.productoid
             left join localidad_producto as c on c.productoid=a.productoid and c.localidadid=a.localidadid
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";
             
             $sqlpag="SELECT a.id,a.cuentaid,a.pago,b.saldo as saldo_cuenta,c.caja,a.descripcion
             FROM compratemp_pago AS a
             INNER JOIN cuenta AS b ON b.id=a.cuentaid
             INNER JOIN formapago AS c ON c.id=b.formapagoid
             WHERE a.usuario_creacion='$usuarioid'
             AND a.localidadid='$localidadid'
             AND a.activo=1;  ";
             
            $dtcab=$this->sqldata($sql);
            $dtdet=$this->sqldata($sqldet);
            $dtpag=$this->sqldata($sqlpag);
            
            $this->validarFinalizar($dtcab,$dtdet,$dtpag);

            $cab=$dtcab[0];
            $id=Guid();

             $sql=" 
             insert into compra(id,numero,localidadid,tipocomprobanteid,proveedorid,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
             select '$id','".$cab["numero"]."','$localidadid','".$cab["tipocomprobanteid"]."',
             '".$cab["proveedorid"]."',
             '".$cab["total"]."',
             '".$cab["pago"]."',
             '".$cab["saldo"]."',
             1,'$usuarioid',$hoy ";

             $array = array($sql);
                $correlativo=1;

            if($this->existePedidoCompra()){
                $sql="update pedidocompra
                set estado='CMP',
                usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                where estado='APR' and activo=1 and localidadid!='$localidadid'";
                array_push($array,$sql);

                $sql="update pedidocompra
                set estado='ATE',
                usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                where estado='APR' and activo=1 and localidadid='$localidadid'";
                array_push($array,$sql);
            }
             foreach($dtdet as $det){
                $productoid=$det["productoid"];
                $cantidad=$det["cantidad"];
                    
                $sql="insert into compra_detalle(id,correlativo,compraid,localidadid,productoid,descripcion,cantidad,precio,importe,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$id','$localidadid','".$productoid."','".$det["descripcion"]."',
                '".$det["cantidad"]."',
                '".$det["precio"]."',
                '".$det["importe"]."',
                1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $precio=$det["precio"];
                $sql="update producto set
                precio_compra = '$precio',
                usuario_modificacion='$usuarioid',
                fecha_hora_modificacion= $hoy
                where id='$productoid'";
                array_push($array,$sql);
               
                if($det["stock"]=="SI"){    
                    $precio_stock= $det["precio_stock"];
                    $cantidad_stock= $det["stock_actual"];    
                   
                    $tipo="ING";
                    $descripcion = "CO ".$cab["tipocomprobante_nombre"]."-".$cab["numero"]." - INGRESO POR COMPRA: ".$cab["proveedor_nombre"];

                    if($det["locprod"]=="N"){
                        $precio_stock=$precio;
                        $cantidad_stock = $cantidad;
                        $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,precio,activo,usuario_creacion,fecha_hora_creacion)
                        values(uuid(),'$localidadid','$productoid',0,'$precio_stock',1,'$usuarioid',$hoy)";
                        array_push($array,$sql);
                    }
                    else{
                        $saldo_monto_stock_actual=$precio_stock*$cantidad_stock;
                        $saldo_monto_stock__stock_nuevo = $precio*$cantidad;
                        $saldo_monto_total=$saldo_monto_stock_actual+$saldo_monto_stock__stock_nuevo;
                        $cantidad_stock=$det["stock_actual"] + $cantidad;
                        $precio_stock= $saldo_monto_total/$cantidad_stock;
                    }

                    
                    $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,precio,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),'$localidadid','$productoid','$descripcion','$tipo', '$cantidad' ,'$cantidad_stock','$precio_stock',1,'$usuarioid',$hoy)";
                    array_push($array,$sql);

                    $sql="update localidad_producto set
                    cantidad= '$cantidad_stock' , precio='$precio_stock',
                    usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                    where localidadid='$localidadid' and productoid='$productoid' ";
                    array_push($array,$sql);
                }
                else{
                    $sqldes="SELECT a.itemid, a.cantidad,  
                    case when c.id is null then 'N' else 'S' end as locprod,
                    case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
                    b.stock,ifnull(c.precio,0.00) as precio_stock
                    FROM producto_destino AS a
                    INNER JOIN producto AS b ON b.id=a.itemid
                    left join localidad_producto as c on c.productoid=a.productoid and c.localidadid='$localidadid'
                    WHERE a.productoid='$productoid' and a.activo=1";

                    $dtdes=$this->sqldata($sqldes);

                    if(count($dtdes)>0){
                        foreach($dtdes as $des){
                            $itemid=$des["itemid"];
                            $precio_stock=$des["precio_stock"];
                            $cantidad_stock= $des["stock_actual"];  
                          
                            if($des["stock"]=="SI"){
                                if($des["cantidad"]>0){       
                                    $cantidad=$des["cantidad"]*$det["cantidad"];
                                    $preciodes=$det["precio"]/$cantidad;               
                                    if($det["locprod"]=="N"){
                                        $precio_stock = $preciodes;
                                        $cantidad_stock = $cantidad;
                                        $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,precio,activo,usuario_creacion,fecha_hora_creacion)
                                        values(uuid(),'$localidadid','".$itemid."',0,'$precio_stock',1,'$usuarioid',$hoy)";
                                        array_push($array,$sql);
                                    }
                                    else{
                                        $saldo_monto_stock_actual=$precio_stock*$cantidad_stock;
                                        $saldo_monto_stock__stock_nuevo = $precio*$cantidad;
                                        $saldo_monto_total=$saldo_monto_stock_actual+$saldo_monto_stock__stock_nuevo;
                                        $cantidad_stock=$des["stock_actual"] + $cantidad;
                                        $precio_stock= $saldo_monto_total/$cantidad_stock;
                                    }
                                    

                                    $sql="update producto set
                                    precio_compra = '".$preciodes."',
                                    usuario_modificacion='$usuarioid',
                                    fecha_hora_modificacion= $hoy
                                    where id='$itemid'";
                                    array_push($array,$sql);

                                    $nuevo_saldo=$des["stock_actual"] + $cantidad;
                                    $tipo="ING";
                                    $descripcion = "CO ".$cab["tipocomprobante_nombre"]."-".$cab["numero"]." - INGRESO POR COMPRA: ".$cab["proveedor_nombre"];
                                    $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,precio,activo,
                                    usuario_creacion,fecha_hora_creacion)
                                    values(uuid(),'$localidadid','".$itemid."','$descripcion','$tipo', '$cantidad','$nuevo_saldo','$precio_stock',1,'$usuarioid',$hoy)";
                                    array_push($array,$sql);
                
                                    $sql="update localidad_producto set
                                    cantidad=$nuevo_saldo , 
                                    precio='$precio_stock',
                                    usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                                    where localidadid='$localidadid' and productoid='".$itemid."' ";
                                    array_push($array,$sql);
                                }
                                else{
                                    $sql="insert into compra_destino(id,compraid,localidadid,productoid,destinoid,cantidad_compra,cantidad_destino,precio,
                                    activo,usuario_creacion,fecha_hora_creacion)
                                    values(uuid(),'$id','$localidadid','".$det["productoid"]."','".$itemid."','".$det["cantidad"]."',0,'".$det["precio"]."',1,'$usuarioid',$hoy)";
                                    array_push($array,$sql);
                                }
                            }
                        }
                    }
                }
                $correlativo++;
             }
             
             foreach($dtpag as $pag){
                $sql="insert into compra_pago(id,correlativo,compraid,cuentaid,descripcion,pago,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$id','".$pag["cuentaid"]."','".$pag["descripcion"]."','".$pag["pago"]."',1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $descripcion = "CO ".$cab["tipocomprobante_nombre"]."-".$cab["numero"]." - INGRESO POR COMPRA: ".$cab["proveedor_nombre"];
                if($pag["caja"]=="SI"){
                    $dtcaja=$this->obtenerCajaAbierta();
                    if(count($dtcaja)>0){
                        $caja=$this->obtenerCajaAbierta()[0]; 
                        $odet=new ECajaDetalle(null);
                        $odet->tipo="SAL";
                        $odet->descripcion=$descripcion;
                        $odet->monto=$pag["pago"]*-1;
                        $odet->saldo=$caja["saldo"]+$odet->monto;
                        $odet->cajaid=$caja["id"];

                        $sql=$this->sqlInsert("caja_detalle",$odet);
                        array_push($array,$sql);

                        $sql=$this->sqlUpdateSum("caja",$caja["id"],"saldo",$odet->monto);
                        array_push($array,$sql);
                    }
                    
                }

                $odet=new ECuentaDetalle();
                $odet->tipo="SAL";
                $odet->descripcion=$descripcion;
                $odet->monto=$pag["pago"]*-1;
                $odet->saldo=$pag["saldo_cuenta"]+$odet->monto;
                $odet->cuentaid= $pag["cuentaid"];
                $sql=$this->sqlInsert("cuenta_detalle",$odet); 
                array_push($array,$sql);

                $sql=$this->sqlUpdateSum("cuenta",$pag["cuentaid"],"saldo",$odet->monto);
                array_push($array,$sql);
             }
            
            $sql="delete from compratemp_detalle
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $sql="delete from compratemp_pago
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $sql="delete from compratemp
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

             $this->db->transacm($array,"Se generó una Compra N° ".$cab["tipocomprobante_nombre"]."-".$cab["numero"]);
        }

        function validarFinalizar($dtcab,$dtdet,$dtpag){
            $details = array();
            $credito="NO";
            if(count($dtcab)==0){
                array_push($details,"No hay una compra pendiente para grabar");
            }
            else{
                $tipocomprobanteid=$dtcab[0]["tipocomprobanteid"];
                $proveedorid=$dtcab[0]["proveedorid"];
                $credito=$dtcab[0]["credito"];
                $saldo=$dtcab[0]["saldo"];
                if(!isGuid($tipocomprobanteid)){
                    array_push($details,"Debe seleccionar un tipo de comprobante");
                }
                if(!isGuid($proveedorid)){
                    array_push($details,"Debe seleccionar un proveedor");
                }
            }
            if(count($dtdet)==0){
                array_push($details,"Debe haber por lo menos un producto");
            }
            else{
                if($credito=="NO"){
                    if(count($dtpag)==0){
                        array_push($details,"Debe haber por lo menos un pago");
                    }
                    else{
                        if($saldo>0){
                            array_push($details,"El pago debe ser igual al total de la compra");
                        }
                    }
                }
            }
            if(count($details)){
                $this->gotoErrorDetails("Ocurrieron algunos errores",$details); 
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

        public function cargarPedidos(){
            $this->validarLocal();
            $this->limpiarDetalle();
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $hoy=now();
            
            $sql="INSERT INTO compratemp_detalle(id,localidadid,productoid,descripcion,cantidad,precio,importe,pedido,activo,
            usuario_creacion,fecha_hora_creacion)
            SELECT UUID() AS id,'$localidadid' AS localidadid,C.id AS productoid,c.nombre AS descripcion,SUM(a.cantidad) AS cantidad,
            0.00 as precio,0.00 AS importe,'SI',
            1 AS activo,'$usuarioid' AS usuario_creacion,$hoy AS fecha_hora_creacion
            FROM pedidocompra_detalle AS a
            INNER JOIN pedidocompra AS b ON b.id=a.pedidocompraid
            INNER JOIN producto AS c ON c.id=a.productoid
            WHERE a.activo=1 AND b.estado='APR' AND b.toma='SI'
            AND b.activo=1
            GROUP BY c.id,c.nombre
            ORDER BY c.nombre";

            $this->db->execute($sql);
            $this->actualizarTotal();
            $this->obtenerCabecera();
           
        }
        public function limpiarDetalle(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sqldet=" delete
             from ".$this->table."_detalle
             where localidadid='$localidadid'
             and usuario_creacion='$usuarioid' ";
             $this->db->execute($sqldet);
        }

        public function existePedidoCompra(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $state=false;
             $sql=" select id
             from ".$this->table."_detalle
             where localidadid='$localidadid'
             and usuario_creacion='$usuarioid' 
             and activo=1 and pedido='SI'";
             
             $dt = $this->sqldata($sql);
             if(count($dt)>0){
                $state = true;
             }
             return $state;
        }

    }
?>