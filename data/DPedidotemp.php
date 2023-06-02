    <?php
    usingdb("tipopedido");
    usingdb("ubicacion");
        class DPedidotemp extends Model{
            private $table="pedido";
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
                    else{
                        $message="No es una local de venta";
                        $this->gotoError($message);
                    }
                }
            }

            public function listar($o){
                $localidadid=auth::local();
                $sql="select a.id,a.tipocomprobanteid,a.clienteid, a.total,a.pago,a.saldo,
                ifnull(b.nombre,'CLIENTE GENERICO') as cliente_nombre,
                ifnull(b.direccion,'SIN DIRECCION') as direccion,
                ifnull(c.nombre_corto,'SIN COMPROBANTE') as tipocomprobante_nombre,
                a.tipopedido,d.nombre AS tipopedido_nombre,
                a.ubicacionid,ifnull(e.nombre,'SIN UBICACION') as ubicacion_nombre,
                DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
                f.nombre as usuario_nombre,
                CONCAT('# ',a.numero,' - ',d.nombre) as numero
                from pedido as a
                INNER JOIN tipopedido as d ON d.clave=a.tipopedido
                left join cliente as b on b.id=a.clienteid
                left join tipocomprobante as c on c.id=a.tipocomprobanteid
                LEFT JOIN ubicacion AS e ON e.id=a.ubicacionid
                left join usuario as f on f.id=a.usuario_creacion
                where a.localidadid='$localidadid'  
                and a.activo=1
                order by a.fecha_hora_creacion desc";
                $this->sqlread($sql);
            }
            public function obtener($o){
                $this->validarLocal();
                $usuarioid=auth::user();
                $localidadid=auth::local();
    
                $sql="select a.id,a.tipocomprobanteid,a.clienteid,a.numero,a.total,a.pago,a.saldo,
                ifnull(b.nombre,'CLIENTE GENERICO') as cliente_nombre,
                ifnull(b.direccion,'SIN DIRECCION') as direccion,
                ifnull(c.nombre_corto,'SIN COMPROBANTE') as tipocomprobante_nombre,
                a.tipopedido,d.nombre AS tipopedido_nombre,
                a.ubicacionid,ifnull(e.nombre,'SIN UBICACION') as ubicacion_nombre,
                DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
                f.nombre as usuario_nombre
                from pedido as a
                INNER JOIN tipopedido as d ON d.clave=a.tipopedido
                left join cliente as b on b.id=a.clienteid
                left join tipocomprobante as c on c.id=a.tipocomprobanteid
                LEFT JOIN ubicacion AS e ON e.id=a.ubicacionid
                left join usuario as f on f.id=a.usuario_creacion
                where a.localidadid='$localidadid' 
                and a.id='$o->id'
                and a.activo=1";

                $sqldet=" select id,productoid,descripcion,cantidad,precio,importe
                from pedido_detalle
                where localidadid='$localidadid' 
                and pedidoid='$o->id'
                and activo=1";
                
                $sqltipocomprobante="select id,nombre,nombre_corto from tipocomprobante where activo=1 order by nombre";

            
                $sqlpago=" select a.id,a.cuentaid,a.descripcion,a.monto,a.pago,a.vuelto,c.nombre as formapago_nombre,c.caja
                from pedido_pago as a
                inner join cuenta as b on b.id=a.cuentaid
                inner join formapago as c on c.id=b.formapagoid
                where a.localidadid='$localidadid' 
                and a.pedidoid='$o->id'
                and a.activo=1
                order by a.fecha_hora_creacion asc";

                $sqlcuenta="select a.id,a.nombre,b.caja,b.nombre as formapago_nombre
                from cuenta a
                INNER JOIN formapago AS b ON b.id=a.formapagoid
                where a.usuarioid='$usuarioid' 
                and a.activo=1 and b.caja='SI'
                union all
                SELECT a.id,a.nombre,b.caja,b.nombre as formapago_nombre
                FROM cuenta AS a
                INNER JOIN formapago AS b ON b.id=a.formapagoid
                WHERE a.venta='SI' AND b.caja='NO'
                AND a.activo=1";

               

                $cab= new EPedidotemp($this->sqlgetrow($sql));
                if($cab->tipocomprobanteid==""){ $cab->tipocomprobanteid="X";}
                if($cab->total==""){ $cab->total="0.00";}
                if($cab->pago==""){ $cab->pago="0.00";}
                if($cab->saldo==""){ $cab->saldo="0.00";}
                $data["cabecera"]=$cab;
                $data["detalle"]=$this->sqldata($sqldet);
                $data["pago"]=$this->sqldata($sqlpago);
                $data["tipocomprobante"]=$this->sqldata($sqltipocomprobante);
                $data["cuenta"]=$this->sqldata($sqlcuenta);

                $this->gotoSuccessData($data); 
            }
            public function nuevo($o){
                $this->validarLocal();
                $usuarioid=auth::user();
                $localidadid=auth::local();
                $hoy=now();

                $o->total=0.00;
                $o->pago=0.00;
                $o->saldo=0.00;
                $o->tipocomprobanteid="";
                $o->clienteid="";
                $o->numero=$this->obtenerNumero();

                switch($o->tipopedido){
                    case PARA_LLEVAR:

                        break;
                }
                $this->validar();
                $sql="insert into pedido(id,localidadid,clienteid,tipopedido,tipocomprobanteid,numero,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid', '$o->clienteid','$o->tipopedido','$o->tipocomprobanteid','$o->numero',
                '$o->total','$o->pago','$o->saldo',1,'$usuarioid',".$hoy.")";
                $this->db->execute($sql);
                $this->gotoSuccess("Se grabaron los datos con éxito",$o->id);
            }
            private function validar(){

            } 
            private function obtenerNumero(){
                $sql=" SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 as numero from pedido";
                $row=$this->sqlgetrow($sql);
                return $row["numero"];

            }

            function registrar($o){
                $usuarioid=auth::user();
                $localidadid=auth::local();
            
                $hoy=now();
                $sqltable="";
           
                    $sqltable="update pedido
                    set tipocomprobanteid='$o->tipocomprobanteid',
                    clienteid = '$o->clienteid',
                    ubicacionid='$o->ubicacionid',
                    tipopedido='$o->tipopedido',
                    usuario_modificacion='$usuarioid',
                    fecha_hora_modificacion=$hoy
                    where localidadid='$localidadid'   and id='$o->id'";
            
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
            public function buscarCliente($o){
                $sql=" select id ,nombre,direccion
                from cliente 
                where activo=1 
                and nombre like  '%".$o->nombre."%'
                order by nombre";
                $this->sqlread($sql);
            }
            public function buscarProducto($o){

                $sql=" select id as productoid,nombre as descripcion,1 as cantidad ,precio_venta as precio
                from producto 
                where activo=1 
                and  venta='SI'
                and nombre like  '%".$o->nombre."%'
                order by nombre";
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
                    $sql="insert into pedido_detalle(id,pedidoid,localidadid,productoid,descripcion,cantidad,precio,importe,activo,usuario_creacion,fecha_hora_creacion)
                    values('$o->id','$o->pedidoid','$localidadid','$o->productoid','$o->descripcion','$o->cantidad','$o->precio','$o->importe',1,'$usuarioid',$hoy)";
                }
                else{
                    $sql="update  pedido_detalle
                    set cantidad = '$o->cantidad', 
                    precio = '$o->precio', 
                    importe = '$o->importe', 
                    fecha_hora_modificacion=$hoy , usuario_modificacion='$usuarioid'
                    where localidadid = '$localidadid'  and productoid='$o->productoid' and pedidoid='$o->pedidoid'"; 
                }

                
                $array = array($sql);
                array_push($array,$this->actualizarTotal($o));
                $this->db->transacx($array);
                $p= new EPedidotemp(null);
                $p->id=$o->pedidoid;
 
                $this->obtenerCabecera($p);
            
            }
            private function actualizarTotal($o){
                $usuarioid=auth::user();
                $localidadid=auth::local();
                $hoy=now();
                $sql="update pedido
                set total=(select ifnull(sum(importe),0) from pedido_detalle where localidadid='$localidadid' and pedidoid='$o->pedidoid'  and activo=1),
                pago=(select ifnull(sum(pago),0) from pedido_pago where localidadid='$localidadid' and pedidoid='$o->pedidoid'  and activo=1),
                saldo=(select ifnull(sum(importe),0) from pedido_detalle where localidadid='$localidadid' and pedidoid='$o->pedidoid' 
                and activo=1)-(select ifnull(sum(pago),0) from pedido_pago where localidadid='$localidadid' and pedidoid='$o->pedidoid'  and activo=1),
                usuario_modificacion='$usuarioid',
                fecha_hora_modificacion=$hoy
                where localidadid='$localidadid' and id='$o->pedidoid' 
                and activo=1";
                return $sql;
            }
            function obtenerCabecera($o){
                $localidadid=auth::local();
                $sql="select a.id,a.tipocomprobanteid,a.clienteid,a.numero,a.total,a.pago,a.saldo,
                ifnull(b.nombre,'CLIENTE GENERICO') as cliente_nombre,
                ifnull(b.direccion,'SIN DIRECCION') as direccion,
                ifnull(c.nombre_corto,'SIN COMPROBANTE') as tipocomprobante_nombre,
                a.tipopedido,d.nombre AS tipopedido_nombre,
                a.ubicacionid,ifnull(e.nombre,'SIN UBICACION') as ubicacion_nombre,
                DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora
                from pedido as a
                INNER JOIN tipopedido as d ON d.clave=a.tipopedido
                left join cliente as b on b.id=a.clienteid
                left join tipocomprobante as c on c.id=a.tipocomprobanteid
                LEFT JOIN ubicacion AS e ON e.id=a.ubicacionid
                where a.localidadid='$localidadid' 
                and a.id='$o->id'
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

                $localidadid=auth::local();

                $sql=" delete
                from pedido_detalle
                where localidadid='$localidadid'
                and productoid='$o->productoid' 
                and pedidoid='$o->pedidoid' 
                and id='$o->id'";

                $array = array($sql);
                array_push($array,$this->actualizarTotal($o));
                $this->db->transacx($array);
 
                $p= new EPedidotemp(null);
                $p->id=$o->pedidoid;

                $this->obtenerCabecera($p);
            }

            public function listarPago($o){
                $localidadid=auth::local();

                $sqlpago=" select a.id,a.cuentaid,a.descripcion,a.monto,a.pago,a.vuelto,c.nombre as formapago_nombre,c.caja
                from pedido_pago as a
                inner join cuenta as b on b.id=a.cuentaid
                inner join formapago as c on c.id=b.formapagoid
                where a.localidadid='$localidadid' 
                and a.pedidoid ='$o->id'
                and a.activo=1
                order by a.fecha_hora_creacion asc";

                $data["pago"]=$this->sqldata($sqlpago);
                $this->gotoSuccessData($data); 
            }
            public function listarPagoCuenta(){
                $usuarioid=auth::user();
              
                $sqldet="select a.id,a.nombre,b.caja,b.nombre
                from cuenta a
                INNER JOIN formapago AS b ON b.id=a.formapagoid
                where a.usuarioid='$usuarioid' 
                and a.activo=1 and b.caja='SI'
                union all
                SELECT a.id,a.nombre,b.caja
                FROM cuenta AS a
                INNER JOIN formapago AS b ON b.id=a.formapagoid
                WHERE a.venta='SI' AND b.caja='NO'
                AND a.activo=1";

                $data["cuenta"]=$this->sqldata($sqldet);
                $this->gotoSuccessData($data); 
            }
            public function eliminarPago($o){
                $usuarioid=auth::user();
                $localidadid=auth::local();

                $sql=" delete
                from pedido_pago
                where localidadid='$localidadid'
                and id='$o->id' 
                and usuario_creacion='$usuarioid' ";

                $array = array($sql);
                array_push($array,$this->actualizarTotal($o));
                $this->db->transacx($array);

                $p= new EPedidotemp(null);
                $p->id=$o->pedidoid;
                $this->obtenerCabecera($p);
            }
            public function registrarPago($o){
                $this->validarLocal();
                $usuarioid=auth::user();
                $localidadid=auth::local();
                $hoy=now();
                $o->id=Guid();
                $cuenta=$this->validarCuenta($o);
                $o->descripcion=$cuenta["nombre"];

                $sql="insert into pedido_pago(id,pedidoid,localidadid,cuentaid,descripcion,pago,monto,vuelto,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$o->pedidoid','$localidadid','$o->cuentaid','$o->descripcion','$o->pago','$o->monto','$o->vuelto',1,'$usuarioid',$hoy)";
                $array = array($sql);
                array_push($array,$this->actualizarTotal($o));
                $this->db->transacx($array);

                $p= new EPedidotemp(null);
                $p->id=$o->pedidoid;
                $this->obtenerCabecera($p);
            
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
            function finalizar($o){
                $this->validarLocal();
                $usuarioid=auth::user();
                $localidadid=auth::local();
            
                $hoy=now();

                $sql="select a.id,a.tipocomprobanteid,a.clienteid,a.numero,a.total,a.pago,a.saldo,
                ifnull(b.nombre,'CLIENTE GENERICO') as cliente_nombre,
                ifnull(b.direccion,'SIN DIRECCION') as direccion,
                ifnull(c.nombre_corto,'SIN COMPROBANTE') as tipocomprobante_nombre,
                a.tipopedido,d.nombre AS tipopedido_nombre,
                a.ubicacionid,ifnull(e.nombre,'SIN UBICACION') as ubicacion_nombre,
                DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
                f.nombre as usuario_nombre
                from pedido as a
                INNER JOIN tipopedido as d ON d.clave=a.tipopedido
                left join cliente as b on b.id=a.clienteid
                left join tipocomprobante as c on c.id=a.tipocomprobanteid
                LEFT JOIN ubicacion AS e ON e.id=a.ubicacionid
                left join usuario as f on f.id=a.usuario_creacion
                where a.localidadid='$localidadid' 
                and a.id='$o->id'
                and a.activo=1";


                $sqldet=" select a.id,a.productoid,a.descripcion,a.cantidad,a.precio,a.importe,
                case when c.id is null then 'N' else 'S' end as locprod,
                case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
                b.stock,ifnull(c.precio,0.00) as precio_stock
                from pedido_detalle as a
                inner join producto as b on b.id=a.productoid
                left join localidad_producto as c on c.productoid=a.productoid and c.localidadid=a.localidadid
                where a.localidadid='$localidadid' 
                and a.pedidoid='$o->id'
                and a.activo=1 ";
                
                $sqlpag="SELECT a.id,a.cuentaid,a.pago,b.saldo as saldo_cuenta,c.caja,a.descripcion
                FROM pedido_pago AS a
                INNER JOIN cuenta AS b ON b.id=a.cuentaid
                INNER JOIN formapago AS c ON c.id=b.formapagoid
                WHERE  a.localidadid='$localidadid'
                and a.pedidoid='$o->id'
                AND a.activo=1;  ";
                
                $dtcab=$this->sqldata($sql);
                $dtdet=$this->sqldata($sqldet);
                $dtpag=$this->sqldata($sqlpag);
                
                $this->validarFinalizar($dtcab,$dtdet,$dtpag);

                $cab=$dtcab[0];
                $id=Guid();

                $sql=" 
                insert into venta(id,numero,localidadid,tipocomprobanteid,clienteid,pedidoid,total,pago,saldo,activo,usuario_creacion,fecha_hora_creacion)
                select '$id', (SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 from venta),'$localidadid','".$cab["tipocomprobanteid"]."',
                '".$cab["clienteid"]."',
                '".$cab["id"]."',
                '".$cab["total"]."',
                '".$cab["pago"]."',
                '".$cab["saldo"]."',
                1,'$usuarioid',$hoy ";

                $array = array($sql);
                    $correlativo=1;

                
                foreach($dtdet as $det){
                    $productoid=$det["productoid"];
                    $cantidad=$det["cantidad"];
                        
                    $sql="insert into venta_detalle(id,correlativo,ventaid,localidadid,productoid,descripcion,cantidad,precio,importe,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),$correlativo,'$id','$localidadid','".$productoid."','".$det["descripcion"]."',
                    '".$det["cantidad"]."',
                    '".$det["precio"]."',
                    '".$det["importe"]."',
                    1,'$usuarioid',$hoy)";
                    array_push($array,$sql);

                    $precio=$det["precio"];
                      
                
                    if($det["stock"]=="SI"){    
                        $cantidad=$cantidad*-1;
                        $precio_stock= $det["precio_stock"];
                        $cantidad_stock= $det["stock_actual"];    
                    
                        $tipo="SAL";
                        $descripcion = "PED ".$cab["numero"]." - VENTA";

                        
                            $saldo_monto_stock_actual=$precio_stock*$cantidad_stock;
                            $saldo_monto_stock__stock_nuevo = $precio_stock*$cantidad;

                            $saldo_monto_total=$saldo_monto_stock_actual+$saldo_monto_stock__stock_nuevo;
                            $cantidad_stock=$det["stock_actual"] + $cantidad;
                            
                       
                        
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
                        $sqldes = "select a.id,a.itemid as productoid,b.nombre AS descripcion,a.cantidad,
                            case when c.id is null then 'N' else 'S' end as locprod,
                            case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
                            b.stock,ifnull(c.precio,0.00) as precio_stock
                            FROM producto_insumo AS a
                            inner join producto as b on b.id=a.itemid
                            left join localidad_producto as c on c.productoid=a.itemid and c.localidadid='$localidadid'
                            WHERE a.productoid = '".$det["productoid"]."'
                            AND a.activo = 1";

                        $dtdes=$this->sqldata($sqldes);
                        $cantidad = $cantidad*-1;
                        if(count($dtdes)>0){
                            foreach($dtdes as $des){
                                $itemid=$des["itemid"];
                                $precio_stock=$des["precio_stock"];
                                $cantidad_stock= $des["stock_actual"];  
                            
                                if($des["stock"]=="SI"){
                                    if($des["cantidad"]>0){       
                                        $cantidad=$des["cantidad"]*$det["cantidad"];
                                        
                                        $saldo_monto_stock_actual=$precio_stock*$cantidad_stock;
                                        $saldo_monto_stock__stock_nuevo = $precio*$cantidad;
                                        $saldo_monto_total=$saldo_monto_stock_actual+$saldo_monto_stock__stock_nuevo;
                                        $cantidad_stock=$des["stock_actual"] + $cantidad;
                                        $precio_stock= $saldo_monto_total/$cantidad_stock;                                        
 
                                        $nuevo_saldo=$des["stock_actual"] + $cantidad;
                                        $tipo="ING";
                                        $descripcion = "CO ".$cab["tipocomprobante_nombre"]."-".$cab["numero"]." - INGRESO POR COMPRA: ".$cab["cliente_nombre"];
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
                                    
                                }
                            }
                        }
                    }
                    $correlativo++;
                }
                
                foreach($dtpag as $pag){
                    $sql="insert into venta_pago(id,correlativo,ventaid,cuentaid,descripcion,pago,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),$correlativo,'$id','".$pag["cuentaid"]."','".$pag["descripcion"]."','".$pag["pago"]."',1,'$usuarioid',$hoy)";
                    array_push($array,$sql);

                    $descripcion = "PED -".$cab["numero"]." - VENTA: ".$cab["cliente_nombre"];
                    if($pag["caja"]=="SI"){
                        $dtcaja=$this->obtenerCajaAbierta();
                        if(count($dtcaja)>0){
                            $caja=$this->obtenerCajaAbierta()[0]; 
                            $odet=new ECajaDetalle(null);
                            $odet->tipo="ING";
                            $odet->descripcion=$descripcion;
                            $odet->monto=$pag["pago"];
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
                
           

                $this->db->transacm($array,"Se generó una Pedido N° ".$cab["tipocomprobante_nombre"]."-".$cab["numero"]);
            }

            function validarFinalizar($dtcab,$dtdet,$dtpag){
                $details = array();
                $localidadid=auth::local();
                if(count($dtcab)==0){
                    array_push($details,"No hay una pedido pendiente para grabar");
                }
                else{
                    $tipopedido=$dtcab[0]["tipopedido"];
                    $clienteid=$dtcab[0]["clienteid"];
                    $ubicacionid=$dtcab[0]["ubicacionid"];
                    $direccion=$dtcab[0]["direccion"];
                    $saldo=$dtcab[0]["saldo"];
                    
                    if(!isGuid($clienteid)){
                        array_push($details,"Debe seleccionar un cliente");
                    }

                    switch($tipopedido){
                        case DELIVERY:
                            if(!isGuid($ubicacionid)){
                                array_push($details,"Debe ingresar una ubicación");
                            }
                            break;
                        case DELIVERY:
                            if($direccion==""){
                                array_push($details,"El cliente debe tener una direccion");
                            }
                            break;
                    }

                   
                    
                }
                if(count($dtdet)==0){
                    array_push($details,"Debe haber por lo menos un producto");
                }
                else{
                    foreach($dtdet as $row){
                        if($row["stock"]=="SI"){
                            if($row["cantidad"]>$row["stock_actual"]){
                                array_push($details,"El producto ".$row["descripcion"]." no tiene stock suficiente");
                            }
                        }
                        else{
                            $sql = "select a.id,a.itemid as productoid,b.nombre AS descripcion,a.cantidad,
                            case when c.id is null then 'N' else 'S' end as locprod,
                            case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
                            b.stock,ifnull(c.precio,0.00) as precio_stock
                            FROM producto_insumo AS a
                            inner join producto as b on b.id=a.itemid
                            left join localidad_producto as c on c.productoid=a.itemid and c.localidadid='$localidadid'
                            WHERE a.productoid = '".$row["productoid"]."'
                            AND a.activo = 1";
                            $dtins=$this->sqldata($sql);

                            foreach($dtins as $ins){
                                if($ins["stock"]=="SI"){
                                    if($row["cantidad"]*$ins["cantidad"] >$ins["stock_actual"]){
                                        array_push($details,$row["descripcion"].": El insumo ".$ins["descripcion"]." no tiene stock suficiente");
                                    }
                                }
                            }
                        }
                    }



                    if(count($dtpag)==0){
                        array_push($details,"Debe haber por lo menos un pago");
                    }
                    else{
                        if($saldo>0){
                            array_push($details,"El pago debe ser igual al total de la pedido");
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

            public function limpiarDetalle(){
                $usuarioid=auth::user();
                $localidadid=auth::local();

                $sqldet=" delete
                from ".$this->table."_detalle
                where localidadid='$localidadid'
                and usuario_creacion='$usuarioid' ";
                $this->db->execute($sqldet);
            }

            public function existePedidoPedido(){
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

            public function listarTipopedido(){
                $this->validarLocal();
                $sql=db_tipopedido_listar();
                $this->sqlread($sql);
            }
            public function listarUbicacion(){
                $localidadid=auth::local();
                $this->sqlread(db_ubicacion_listar($localidadid));
            }
        }
    ?>