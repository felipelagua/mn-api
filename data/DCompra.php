<?php
    usingdb("caja");
    usingdb("localidadproducto");
    
    class DCompra extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            concat(b.nombre_corto,' ',a.numero) as numero,e.nombre AS proveedor_nombre,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre as proveedor_nombre,
            a.total,a.pago,a.saldo
            FROM compra AS a
            INNER JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            inner join proveedor as e on e.id=a.proveedorid 
             WHERE a.localidadid='$localidadid'";
          
            if($o->tipo=="M"){
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y') = '$o->anio'
                and date_format(a.fecha_hora_creacion,'%m') = '$o->mes'";
            }
            else{
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y-%m-%d') between  '$o->desde' and  '$o->hasta'";
            }
            $sql.=" and ('$o->usuariocreador'='X' or a.usuario_creacion='$o->usuariocreador')";
            $sql.=" and ('$o->nombre'='' or (a.numero='$o->nombre' or e.nombre like '%$o->nombre%'))";
            $sql.=" order by a.fecha_hora_creacion desc";
             
           
            $this->sqlread($sql);
             
        }
        public function listarPendiente($o){
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            concat(b.nombre_corto,' ',a.numero) as numero,e.nombre AS proveedor_nombre,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre as proveedor_nombre,
            a.total,a.pago,a.saldo
            FROM compra AS a
            INNER JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            inner join proveedor as e on e.id=a.proveedorid 
             WHERE a.activo=1 and a.saldo>0 and e.nombre like '%$o->nombre%' ";
            $sql.=" order by a.fecha_hora_creacion desc";
             
           
            $this->sqlread($sql);
             
        }
        public function obtener($o){
            $sql=" SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS tipocomprobante_nombre,
            a.total,a.saldo,a.pago,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre as proveedor_nombre
            FROM compra AS a
            INNER JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            INNER JOIN proveedor AS e ON e.id=a.proveedorid
            WHERE a.id='$o->id'";
           
            $sqldet="SELECT a.descripcion,a.cantidad,a.precio,a.importe,b.stock,
            case when (select count(1) from producto_destino as c where c.productoid=b.id and activo=1) >0 then 'SI' else 'NO' end as destino
            FROM compra_detalle a
            inner join producto as b on b.id=a.productoid
            where a.compraid='$o->id'
            and a.activo=1
            ORDER BY a.correlativo,a.fecha_hora_creacion";

            $sqlpag="SELECT descripcion,pago
            FROM compra_pago
            where compraid='$o->id'
            and activo=1
            ORDER BY fecha_hora_creacion";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $data["pago"]=$this->sqldata($sqlpag);
            $this->gotoSuccessData($data); 
        }
        public function obtenerFiltros(){
            $hoy=now();
            
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format($hoy,'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM compra";
            $sqlusuario="SELECT id,nombre FROM usuario WHERE id IN (SELECT usuario_creacion FROM compra WHERE activo=1) AND activo=1";
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
            $this->gotoSuccessData($data); 
 
        }
        public function listarDestinoPendiente(){
            $localidadid=auth::local();
            $sql="SELECT a.id,b.nombre AS producto_nombre,c.nombre AS destino_nombre,
            cast(a.cantidad_destino as decimal(10,2)) as cantidad_destino,
            cast(a.cantidad_compra as decimal(10,2)) as cantidad_compra,
            DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora
            FROM compra_destino AS a
            INNER JOIN producto AS b ON b.id=a.productoid
            INNER JOIN producto AS c ON c.id=a.destinoid
            WHERE a.localidadid='$localidadid'
            AND a.cantidad_destino=0
            ORDER BY a.fecha_hora_creacion";
            $this->sqlread($sql);             
        }
        public function actualizarDestino($o){
            $hoy=now();
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sql="SELECT a.id,b.nombre AS producto_nombre,c.nombre AS destino_nombre,
            cast(a.cantidad_destino as decimal(10,2)) as cantidad_destino,
            cast(a.cantidad_compra as decimal(10,2)) as cantidad_compra,
            case when d.id is null then 'N' else 'S' end as locprod,
            case when d.cantidad is null then 0 else d.cantidad end as stock_actual,
            ifnull(d.precio,0.00) as precio_stock,a.precio,a.destinoid,
            b.stock
            FROM compra_destino AS a
            INNER JOIN producto AS b ON b.id=a.productoid
            INNER JOIN producto AS c ON c.id=a.destinoid
            left join localidad_producto as d on d.localidadid=a.localidadid and d.productoid=a.destinoid
            WHERE a.localidadid='$localidadid' and a.activo=1
            AND a.id = '$o->id'";

            $dtdes=$this->sqldata($sql);
           $this->validarDetino($dtdes,$o);
           $des=$dtdes[0];
            $array = array();
            $itemid=$des["destinoid"];
            $cantidad=$o->cantidad;
            $preciodes =$des["precio"]/$o->cantidad;
            $precio_stock=$des["precio_stock"];
            $cantidad_stock= $des["stock_actual"];

            if($des["locprod"]=="N"){
                $precio_stock = $preciodes;
                $cantidad_stock = $cantidad;
                $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,precio,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),'$localidadid','".$itemid."',0,'$precio_stock',1,'$usuarioid',$hoy)";
                array_push($array,$sql);
            }
            else{
                $saldo_monto_stock_actual=$precio_stock*$cantidad_stock;
                $saldo_monto_stock__stock_nuevo = $preciodes*$cantidad;
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
            $descripcion = "POR COMPRA DE: ".$des["cantidad_compra"]." ".$des["producto_nombre"];
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
            
            $sql="update compra_destino set 
            cantidad_destino='$o->cantidad',
            usuario_modificacion='$usuarioid',
            fecha_hora_modificacion=$hoy
            where id='$o->id' and localidadid='$localidadid'";
            array_push($array,$sql);

            $this->db->transacm($array,"Se grabaron los datos con éxito");
        }
        private function validarDetino($dtdes,$o){
            
            if(count($dtdes)==0){
                $this->gotoError("El registro seleccionado es inválido");
            }
            if($o->cantidad<=0){
                $this->gotoError("La cantidad ingresada debe ser mayor a cero");
            }
            $row=$dtdes[0];
            if($row["cantidad_destino"]>0){
                $this->gotoError("El destino ya tiene agregado una cantidad");
            }
            if($row["stock"]=="SI"){
                $this->gotoError("El destino no tiene habilitado stock");
            }
        }
        public function registrarPago($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $hoy=now();
            $o->id=Guid();
            $cta=$this->sqlrow("select id,nombre,saldo from cuenta where id='$o->cuentaid'");
            $com=$this->sqlrow("select id,proveedorid,correlativo,saldo from compra where id='$o->compraid'");
            $prv=$this->sqlrow("select id,nombre from proveedor where id='".$com["proveedorid"]."'");

            $o->descripcion=$cta["nombre"];

            $sql=db_compra_pago_insertar(1,$o->compraid,$localidadid,$o->cuentaid,$cta["nombre"],$o->pago,$usuarioid,$hoy);
            $array = array($sql); 

            $odet=new ECuentaDetalle();
            $odet->tipo="SAL";
            $odet->descripcion="PAGO A ".$prv["nombre"];
            $odet->monto=$o->pago*-1;
            $odet->saldo=$cta["saldo"]+$odet->monto;
            $odet->cuentaid= $o->cuentaid;
            $sql=$this->sqlInsert("cuenta_detalle",$odet); 
            array_push($array,$sql);

            $sql=$this->sqlUpdateSum("cuenta",$o->cuentaid,"saldo",$odet->monto);
            array_push($array,$sql);
 
            $sql="update compra set pago=(select ifnull(sum(pago),0) from compra_pago where compraid=compra.id) where id='$o->compraid'";
            array_push($array,$sql);

            $sql="update compra set saldo=total-pago,
            usuario_modificacion='$usuarioid',
            fecha_hora_modificacion=$hoy
            where id='$o->compraid'";
            array_push($array,$sql);
            
            $this->db->transacm($array,"Se pagó un servicio correctamente");
           
        }
    }
?>