<?php
    class DNotasalidatemp extends Model{
        private $table="notasalidatemp";
         
        public function obtener(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sqlmotivo="select id,nombre from motivosalida where activo=1 order by nombre";
            $sql=" select id,motivosalidaid,comentario
             from ".$this->table." 
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";

             $sqldet=" select id,productoid,descripcion,cantidad
             from ".$this->table."_detalle
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";
            
             $cab= new ENotasalidatemp($this->sqlgetrow($sql));
            $data["cabecera"]=$cab; 
            $data["detalle"]=$this->sqldata($sqldet);
            $data["motivos"]=$this->sqldata($sqlmotivo);
            $this->gotoSuccessData($data); 
        }

        function registrar($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
        
            $hoy=now();
            $sqltable="";
             if(!$this->existe()){
                $sqltable="insert into notasalidatemp(id,localidadid,motivosalidaid,comentario,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->motivosalidaid','$o->comentario',1,'$usuarioid',".$hoy.")";
             }
             else{
                $sqltable="update notasalidatemp
                set motivosalidaid='$o->motivosalidaid',comentario='$o->comentario'
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

        public function buscarProducto($o){
            $usuarioid=auth::user();
            $localidadid=auth::local();
            $sql=" select a.id as productoid,a.nombre as descripcion,cast((b.cantidad-ifnull(c.cantidad,0.00)) AS DECIMAL(10,0))  as cantidad,
            cast(b.cantidad  AS DECIMAL(10,0)) as stock
            from producto as a
            inner join localidad_producto b on b.productoid=a.id and b.localidadid='$localidadid'
            left join 
            (SELECT productoid,ifnull(SUM(cantidad),0) AS cantidad
            FROM notasalidatemp_detalle
            WHERE localidadid='$localidadid' and usuario_creacion!='$usuarioid' and NOT productoid IS NULL
            GROUP BY productoid) as c on c.productoid=a.id
             where a.activo=1 
             and b.activo=1
             and not a.id in (select productoid from ".$this->table."_detalle
             where localidadid='$localidadid' and usuario_creacion='$usuarioid')
             and a.nombre like  '%".$o->nombre."%'
             order by a.fecha_hora_creacion desc";
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
            FROM notasalidatemp_detalle
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
            $sql=" select a.id,a.motivosalidaid,a.comentario,b.nombre as motivosalida_nombre,
            (SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 from notasalida) as numero
             from ".$this->table." as a 
             inner join motivosalida as b on b.id=a.motivosalidaid
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
             insert into notasalida(id,numero,localidadid,motivosalidaid,comentario,activo,usuario_creacion,fecha_hora_creacion)
             select '$id','".$cab["numero"]."','$localidadid','".$cab["motivosalidaid"]."','".$cab["comentario"]."',1,'$usuarioid',$hoy ";

             $array = array($sql);
                $correlativo=1;
             foreach($dtdet as $det){
                $sql="insert into notasalida_detalle(id,correlativo,notasalidaid,localidadid,productoid,descripcion,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$id','$localidadid','".$det["productoid"]."','".$det["descripcion"]."','".$det["cantidad"]."',1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $productoid=$det["productoid"];
                if($det["locprod"]=="N"){
                    $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),'$localidadid','$productoid',0,1,'$usuarioid',$hoy)";
                    array_push($array,$sql);
                }

                $cantidad=$det["cantidad"]*-1;
                $nuevo_saldo=$det["stock_actual"] + $cantidad;
                $descripcion = "NS ".$cab["numero"]." - SALIDA POR: ".$cab["motivosalida_nombre"];
                $tipo="SAL";
                $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),'$localidadid','$productoid','$descripcion','$tipo', $cantidad ,$nuevo_saldo,1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $sql="update localidad_producto set
                cantidad=$nuevo_saldo , usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                where localidadid='$localidadid' and productoid='$productoid' ";
                array_push($array,$sql);
                $correlativo++;
             }
            $sql="delete from notasalidatemp_detalle
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $sql="delete from notasalidatemp
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
                $motivosalidaid=$dtcab[0]["motivosalidaid"];
                if(!isGuid($motivosalidaid)){
                    array_push($details,"Debe seleccionar un motivo de salida");
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