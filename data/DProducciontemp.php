<?php
    class Dproducciontemp extends Model{
        private $table="producciontemp";
         
        public function obtener(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sql=" select id  
             from ".$this->table." 
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";

             $sqldet=" select id,productoid,descripcion,cantidad
             from ".$this->table."_detalle
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";
            
             $cab= new Eproducciontemp($this->sqlgetrow($sql));
            $data["cabecera"]=$cab;
            $data["detalle"]=$this->sqldata($sqldet); 
            $this->gotoSuccessData($data); 
        }

        function registrar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            $hoy=now();
            $sqltable="";
             if(!$this->existe()){
                $sqltable="insert into producciontemp(id,localidadid,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid',1,'$usuarioid',".$hoy.")";
                $this->db->execute($sqltable);
             }
  
           
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
            $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
            from producto 
             where activo=1 
             and terminado='SI'
             and not id in (select productoid from ".$this->table."_detalle
             where localidadid='$localidadid' and usuario_creacion='$usuarioid')
             and id in (select productoid from producto_insumo where activo=1)
             and nombre like  '%".$o->nombre."%'
             order by fecha_hora_creacion desc";
              $this->sqlread($sql);
        }
        public function listarDetalle(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

             $sqldet=" select id,productoid,descripcion,cantidad
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

            if(!$this->existeDetalle($o)){
                $o->id=Guid();
                $sql="insert into ".$this->table."_detalle(id,localidadid,productoid,descripcion,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->productoid','$o->descripcion','$o->cantidad',1,'$usuarioid',$hoy)";

                $this->db->execute($sql);
                $this->gotoSuccess("Se actualizaron los datos con éxito",$o->id);
            }
            else{
                $sql="update  ".$this->table."_detalle
                set cantidad = '$o->cantidad', fecha_hora_modificacion=$hoy , usuario_modificacion='$usuarioid'
                where localidadid = '$localidadid'  and productoid='$o->productoid' and usuario_creacion='$usuarioid'";
                $this->db->execute($sql);
            $this->gotoSuccess("Se crearon los datos con éxito",$o->id);
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
            $this->gotoSuccess("Se eliminó correctamente",$o->id); 
        }

        function finalizar(){
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            $hoy=now();
            $sql=" select a.id, 
            (SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 from produccion) as numero
             from ".$this->table." as a 
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";

             $sqldet=" select a.id,a.productoid,a.descripcion,a.cantidad,
             case when b.id is null then 'N' else 'S' end as locprod,
             case when b.cantidad is null then 0 else b.cantidad end as stock_actual
             from ".$this->table."_detalle as a
             left join localidad_producto as b on b.productoid=a.productoid and b.localidadid=a.localidadid
             where a.localidadid='$localidadid' 
             and a.usuario_creacion='$usuarioid'
             and a.activo=1";
             
             
            $dtcab=$this->sqldata($sql);
            $dtdet=$this->sqldata($sqldet);

            $this->validarFinalizar($dtcab,$dtdet);

            $cab=$dtcab[0];
            $id=Guid();

             $sql=" 
             insert into produccion(id,numero,localidadid, activo,usuario_creacion,fecha_hora_creacion)
             select '$id','".$cab["numero"]."','$localidadid',1,'$usuarioid',$hoy ";

             $array = array($sql);
                $correlativo=1;
             foreach($dtdet as $det){
                $productoid=$det["productoid"];
                $cantidad=$det["cantidad"];

                $sql="insert into produccion_detalle(id,correlativo,produccionid,localidadid,productoid,descripcion,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$id','$localidadid','".$productoid."','".$det["descripcion"]."','".$cantidad."',1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                if($det["locprod"]=="N"){
                    $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),'$localidadid','$productoid',0,1,'$usuarioid',$hoy)";
                    array_push($array,$sql);
                }
                $nuevo_saldo=$det["stock_actual"] + $cantidad;
                $tipo="ING";
                $descripcion = "PRD ".$cab["numero"]." - INGRESO POR PRODUCCION";
                $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),'$localidadid','$productoid','$descripcion','$tipo', $cantidad ,$nuevo_saldo,1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $sql="update localidad_producto set
                cantidad=$nuevo_saldo , usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                where localidadid='$localidadid' and productoid='$productoid' ";
                array_push($array,$sql);

                $sqldes="SELECT a.itemid, a.cantidad,  
                case when c.id is null then 'N' else 'S' end as locprod,
                case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
                b.stock,ifnull(c.precio,0.00) as precio_stock
                FROM producto_insumo AS a
                INNER JOIN producto AS b ON b.id=a.itemid
                left join localidad_producto as c on c.productoid=a.itemid and c.localidadid='$localidadid'
                WHERE a.productoid='$productoid' and a.activo=1";

                $dtdes=$this->sqldata($sqldes);
                if(count($dtdes)>0){
                    $precio_producto=0;
                    foreach($dtdes as $des){
                        $itemid=$des["itemid"];
                        $precio_stock=$des["precio_stock"];
                        $cantidad_stock= $des["stock_actual"]; 

                        
                        if($des["stock"]=="SI"){
                            if($des["cantidad"]>0){ 
                                $cantidad=$des["cantidad"]*$det["cantidad"];
                               
                                $precio_producto = $precio_producto+ ($precio_stock*$cantidad);
                                $cantidad_stock = $cantidad*-1;
                                $nuevo_saldo=$des["stock_actual"] + $cantidad;
                                $tipo="SAL";
                                $descripcion = "PRD ".$cab["numero"]." - INGRESO POR PRODUCCION";
                                $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,precio,activo,
                                usuario_creacion,fecha_hora_creacion)
                                values(uuid(),'$localidadid','".$itemid."','$descripcion','$tipo', '$cantidad_stock','$nuevo_saldo','$precio_stock',1,'$usuarioid',$hoy)";
                                array_push($array,$sql);

                                $sql="update localidad_producto set
                                cantidad=$nuevo_saldo , 
                                precio='$precio_stock',
                                usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                                where localidadid='$localidadid' and productoid='".$itemid."' ";
                                array_push($array,$sql);
                            }
                        }
                        else{
                            if($des["cantidad"]>0){ 
                                $cantidad=$des["cantidad"]*$det["cantidad"];
                                $cantidad_stock = $cantidad;
                                $precio_producto = $precio_producto+ ($precio_stock*$cantidad);
                            }
                        }
                    }
                    if($precio_producto>0){
                        $sql="update localidad_producto set
                        precio='$precio_producto' 
                        where localidadid='$localidadid' and productoid='$productoid' ";
                        array_push($array,$sql);

                        $sql="update  producto set
                        precio_compra ='$precio_producto' 
                        where id='$productoid' ";
                        array_push($array,$sql);
                    }
                }

                $correlativo++;
             }
            $sql="delete from producciontemp_detalle
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $sql="delete from producciontemp
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

             $this->db->transacm($array,"Se generó un lote de producción N° ".$cab["numero"]);
        }
        function validarFinalizar($dtcab,$dtdet){
            $localidadid=auth::local();
            $details = array();
            if(count($dtcab)==0){
                array_push($details,"No hay una nota de ingreso pendiente para grabar");
            }
   
            if(count($dtdet)==0){
                array_push($details,"Debe haber por lo menos un producto");
            }
            else{
                foreach($dtdet as $det){
                    $productoid = $det["productoid"];
                    $sqldes="SELECT a.itemid, a.cantidad,  b.nombre,
                    case when c.id is null then 'N' else 'S' end as locprod,
                    case when c.cantidad is null then 0 else c.cantidad end as stock_actual,
                    b.stock,ifnull(c.precio,0.00) as precio_stock
                    FROM producto_insumo AS a
                    INNER JOIN producto AS b ON b.id=a.itemid
                    left join localidad_producto as c on c.productoid=a.itemid and c.localidadid='$localidadid'
                    WHERE a.productoid='$productoid' and a.activo=1";

                    $dtdes=$this->sqldata($sqldes);
                    if(count($dtdes)==0){
                        array_push($details,$det["descripcion"]." debe tener insumos relacionados");
                    }
                    else{
                        foreach($dtdes as $des){
                            if($des["stock"]=="SI" && $des["stock_actual"]<$des["cantidad"]){
                                array_push($details,$des["nombre"]." con stock = ".$des["stock_actual"]." debe tener stock suficiente para ".$det["cantidad"]." de ".$det["descripcion"]);
                            }
                        }
                    }
                }
            }
            if(count($details)){
                $this->gotoErrorDetails("Ocurrieron algunos errores",$details); 
            }
        }
    }
?>