<?php
using("db/traslado");
usingdb("localidadcosto");
    class DTrasladotemp extends Model{
        private $table="trasladotemp";
         
        public function obtener(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sqllocalidad="select id,nombre from localidad where activo=1   order by nombre";
            $sqlusuario="select id,nombre from usuario where activo=1 and id!='$usuarioid' order by nombre";

            $sql=" select a.id,case when a.localidaddestinoid='' then 'X' else a.localidaddestinoid end as localidaddestinoid,
            a.solicitadoporid,a.comentario,ifnull(a.pedidocompraid,'') as pedidocompraid,
            ifnull(b.numero,'') as pedidocompra_numero
             from ".$this->table." as a
             left join pedidocompra as b on b.id=a.pedidocompraid
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";

             $sqldet=db_trasladotemp_detalle($localidadid,$usuarioid);
            
             $cab= new ETrasladotemp($this->sqlgetrow($sql));
             if($cab->localidaddestinoid==""){$cab->localidaddestinoid="X";}
             If($cab->solicitadoporid==""){ $cab->solicitadoporid="X";}
            $data["cabecera"]=$cab; 
            $data["detalle"]=$this->sqldata($sqldet);
            $data["localidades"]=$this->sqldata($sqllocalidad);
            $data["solicitantes"]=$this->sqldata($sqlusuario);
            $this->gotoSuccessData($data); 
        }

        function registrar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            if($o->localidaddestinoid=="X"){$o->localidaddestinoid="";}
            If($o->solicitadoporid=="X"){ $o->solicitadoporid="";}

            $hoy=now();
            $sqltable="";
             if(!$this->existe()){
                $sqltable="insert into trasladotemp(id,localidadid,localidaddestinoid,solicitadoporid,comentario,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->localidaddestinoid','$o->solicitadoporid','$o->comentario',1,'$usuarioid',".$hoy.")";
             }
             else{
                $sqltable="update trasladotemp
                set localidaddestinoid='$o->localidaddestinoid',
                solicitadoporid='$o->solicitadoporid',
                comentario='$o->comentario'
                where localidadid='$localidadid' and usuario_creacion='$usuarioid'";
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

        public function buscarProducto($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $this->sqlread(db_traslado_producto_buscar($localidadid,$usuarioid,$o->nombre));
        }
        public function listarDetalle(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sqldet= db_trasladotemp_detalle($localidadid,$usuarioid);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function registrarDetalle($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $hoy=now();

            if(!$this->existeDetalle($o)){
                $this->validarProductoCantidad($o);
                $o->id=Guid();
                $sql="insert into ".$this->table."_detalle(id,localidadid,productoid,descripcion,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->productoid','$o->descripcion','$o->cantidad',1,'$usuarioid',$hoy)";

                $this->db->execute($sql);
                $this->gotoSuccess("Se actualizaron los datos con éxito",$o->id);
            }
            else{
                $this->validarProductoCantidad($o);
                $sql="update  ".$this->table."_detalle
                set cantidad = '$o->cantidad', fecha_hora_modificacion=$hoy , usuario_modificacion='$usuarioid'
                where localidadid = '$localidadid'  and productoid='$o->productoid' and usuario_creacion='$usuarioid'";
                $this->db->execute($sql);
            $this->gotoSuccess("Se crearon los datos con éxito",$o->id);
            }
           
        }
        private function existeDetalle($o){
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
        private function validarProductoCantidad($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=" select a.id as productoid ,cast((b.cantidad-ifnull(c.cantidad,0.00)) AS DECIMAL(10,0))  as cantidaddisponible
            from producto as a
            inner join localidad_producto b on b.productoid=a.id and b.localidadid='$localidadid'
            left join 
            (SELECT productoid,ifnull(SUM(cantidad),0) AS cantidad
            FROM trasladotemp_detalle
            WHERE localidadid='$localidadid' and usuario_creacion='$usuarioid' and NOT productoid IS NULL
            GROUP BY productoid) as c on c.productoid=a.id
             where a.activo=1 
             and b.activo=1
             and a.id='$o->productoid'";

             $dt=$this->sqldata($sql);

             $message="";
             if(count($dt)==0){
                $message="El producto no es válido";  
             }
             else{
                $cantidaddisponible=$dt[0]["cantidaddisponible"];
                if($o->cantidad==0 || $o->cantidad>$cantidaddisponible){
                    $message="La cantidad ingresada no es válida";  
                }
             }
             if($message!=""){
                $this->gotoError($message);
             }
        } 
        public function eliminarDetalle($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sql="select id,pedido from ".$this->table."_detalle 
            where productoid='$o->productoid' 
            and localidadid='$localidadid'
            and usuario_creacion='$usuarioid'
            and activo=1 ";
            $dt=$this->sqldata($sql);

            if(count($dt)>0){
                $pedido=$dt[0]["pedido"];
                if($pedido=="SI"){
                    $this->gotoError("No se puede eliminar el registro porque esta asociado al pedido asignado");
                }
            }
             $sqldet=" delete
             from ".$this->table."_detalle
             where localidadid='$localidadid'
             and productoid='$o->productoid' 
             and usuario_creacion='$usuarioid' ";

             $this->db->execute($sqldet);
            $this->gotoSuccess("Se eliminó correctamente",$o->id); 
        }
        public function asignarPedido($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sql=" update trasladotemp
             set pedidocompraid='$o->pedidocompraid'
             where localidadid='$localidadid' and activo=1
             and usuario_creacion='$usuarioid' ";
             $array = array($sql);

             $sql=" delete from trasladotemp_detalle
             where localidadid='$localidadid'
             and usuario_creacion='$usuarioid' ";
             array_push($array,$sql);

             $sql="INSERT INTO trasladotemp_detalle(id,localidadid,productoid,descripcion,cantidad,pedido,activo,usuario_creacion,fecha_hora_creacion)
             SELECT UUID() AS id,'$localidadid' AS localidadid,c.id AS productoid,c.nombre AS descripcion,SUM(a.cantidad) AS cantidad,
             'SI' AS pedido,1 AS activo,'$usuarioid' AS usuario_creacion,'' AS fecha_hora_creacion
             FROM pedidocompra_detalle AS a
             INNER JOIN pedidocompra AS b ON b.id=a.pedidocompraid
             INNER JOIN producto AS c ON c.id=a.productoid
             WHERE a.activo=1 AND b.estado='CMP' AND b.toma='SI'
             and a.localidadid!='$localidadid'
             AND b.activo=1  and b.id='$o->pedidocompraid'
             and not c.id in (select productoid from producto_destino where activo=1)
             GROUP BY c.id,c.nombre
             UNION ALL
             SELECT UUID() AS id,'$localidadid' AS localidadid,c.id AS productoid,c.nombre AS descripcion,SUM(a.cantidad*d.cantidad) AS cantidad,
             'SI' AS pedido,1 AS activo,'$usuarioid' AS usuario_creacion,'' AS fecha_hora_creacion
             FROM pedidocompra_detalle AS a
             INNER JOIN pedidocompra AS b ON b.id=a.pedidocompraid         
             INNER JOIN producto_destino as d on d.productoid=a.productoid and d.activo=1
             INNER JOIN producto AS c ON c.id=d.itemid
             WHERE a.activo=1 AND b.estado='CMP' AND b.toma='SI'
             and a.localidadid!='$localidadid'
             AND b.activo=1  and b.id='$o->pedidocompraid' 
             GROUP BY c.id,c.nombre
             ORDER BY 4
             ";
             array_push($array,$sql);
             
            $this->db->transacm($array,"Se asignó el pedido correctamente");
        }
        public function quitarPedido(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sql=" update trasladotemp
             set pedidocompraid=''
             where localidadid='$localidadid' and activo=1 
             and usuario_creacion='$usuarioid' ";
             $array = array($sql);

             $sql=" delete from trasladotemp_detalle
             where localidadid='$localidadid' and pedido='SI'
             and usuario_creacion='$usuarioid' ";
             array_push($array,$sql);
 
             
            $this->db->transacm($array,"Se quitó el pedido correctamente");
        }
        function finalizar(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            $hoy=now();
            $sql=db_trasladotemp_obtener($localidadid,$usuarioid);
             $dtcab=$this->sqldata($sql);
             $localidaddestinoid="";
             $pedidocompraid="";
             if(count($dtcab)>0){
                $rowcab=$dtcab[0];
                $localidaddestinoid = $rowcab["localidaddestinoid"];
                $pedidocompraid = $rowcab["pedidocompraid"];
             }
             

             $sqldet=" select a.id,a.productoid,a.descripcion,a.cantidad,
             case when b.id is null then 'N' else 'S' end as locprod,
             case when b.cantidad is null then 0 else b.cantidad end as stock_origen,
             case when c.cantidad is null then 0 else c.cantidad end as stock_destino,
             case when c.cantidad is null then 0 else 1 end as existe_destino,
             d.stock
             from trasladotemp_detalle as a
             inner join producto as d on d.id=a.productoid
             left join localidad_producto as b on b.productoid=a.productoid and b.localidadid=a.localidadid
             left join localidad_producto as c on c.productoid=a.productoid and c.localidadid='$localidaddestinoid'
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";
             
             
            
            $dtdet=$this->sqldata($sqldet);

            $this->validarFinalizar($dtcab,$dtdet);

            $cab=$dtcab[0];
            $id=Guid();
            $estado="REGISTRADO";

            

             $sql=" 
             insert into traslado(id,numero,localidadid,pedidocompraid,localidaddestinoid,solicitadoporid,comentario,estado,activo,usuario_creacion,fecha_hora_creacion)
             select '$id','".$cab["numero"]."','$localidadid','$pedidocompraid','". $localidaddestinoid."','".$cab["solicitadoporid"]."','$estado','".$cab["comentario"]."',1,'$usuarioid',$hoy ";
             $array = array($sql);

             $sql=db_localidad_costo_insertar($localidadid,"TRASLADO",$id,"TRASLADO ORIGEN","REG",$cab["precio_traslado"]*-1,$usuarioid,$hoy);
             array_push($array,$sql);
             $sql=db_localidad_costo_insertar( $localidaddestinoid,"TRASLADO",$id,"TRASLADO DESTINO","SAL",$cab["precio_traslado"],$usuarioid,$hoy);
             array_push($array,$sql);

             if($pedidocompraid!=""){
                $sql="update pedidocompra
                set estado='ATE',
                usuario_modificacion = '$usuarioid',
                fecha_hora_creacion = $hoy
                where id = '$pedidocompraid' ";
                array_push($array,$sql);
             }

             $correlativo=1;
             foreach($dtdet as $det){
                $sql="insert into traslado_detalle(id,correlativo,trasladoid,localidadid,productoid,descripcion,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$id','$localidadid','".$det["productoid"]."','".$det["descripcion"]."','".$det["cantidad"]."',1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $productoid=$det["productoid"];
                $stock=$det["stock"];

                if($stock=="SI"){
                    if($det["locprod"]=="N"){
                        $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                        values(uuid(),'$localidadid','$productoid',0,1,'$usuarioid',$hoy)";
                        array_push($array,$sql);
                    }

                    $cantidad=$det["cantidad"]*-1;
                    $nuevo_saldo=$det["stock_origen"] + $cantidad;
                    $descripcion = "TR ".$cab["numero"]." - ENVIADO A: ".$cab["localidaddestino_nombre"];
                    $tipo="SAL";

                    $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),'$localidadid','$productoid','$descripcion','$tipo', $cantidad ,$nuevo_saldo,1,'$usuarioid',$hoy)";
                    array_push($array,$sql);

                    $sql="update localidad_producto set
                    cantidad=$nuevo_saldo , usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                    where localidadid='$localidadid' and productoid='$productoid' ";
                    array_push($array,$sql);

                    $cantidad=$det["cantidad"];
                    $nuevo_saldo=$det["stock_destino"] + $cantidad;
                    $descripcion = "TR ".$cab["numero"]." - DESDE: ".$cab["localidad_nombre"];
                    $tipo="ING";
                    $localidaddestinoid=$cab["localidaddestinoid"];
                    
                    if($det["existe_destino"]==0){
                        $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                        values(uuid(),'$localidaddestinoid','$productoid',0,1,'$usuarioid',$hoy)";
                        array_push($array,$sql);
                    }
                    $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),'$localidaddestinoid','$productoid','$descripcion','$tipo', $cantidad ,$nuevo_saldo,1,'$usuarioid',$hoy)";
                    array_push($array,$sql);

                    $sql="update localidad_producto set
                    cantidad=$nuevo_saldo , usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                    where localidadid='$localidaddestinoid' and productoid='$productoid' ";
                    array_push($array,$sql);
                }
                $correlativo++;
             }
            $sql="delete from trasladotemp_detalle
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $sql="delete from trasladotemp
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $this->db->transacm($array,"Se generó una Nota de salida N° ".$cab["numero"]);

        }
        function validarFinalizar($dtcab,$dtdet){
            $details = array();
            if(count($dtcab)==0){
                array_push($details,"No hay una nota de salida pendiente para grabar");
            }
            else{
                $localidaddestinoid=$dtcab[0]["localidaddestinoid"];
                $solicitadoporid=$dtcab[0]["solicitadoporid"];
                if(!isGuid($localidaddestinoid)){
                    array_push($details,"Debe seleccionar un local destino");
                }
                if(!isGuid($solicitadoporid)){
                    array_push($details,"Debe seleccionar un solicitante");
                }
            }
            if(count($dtdet)==0){
                array_push($details,"Debe haber por lo menos un producto");
            }
            
            if(count($details)){
                $this->gotoErrorDetails("Ocurrieron algunos errores",$details); 
            }
        }
    }
?>