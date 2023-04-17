<?php
    class DNotaingresotemp extends Model{
        private $table="notaingresotemp";
         
        public function obtener(){
            $usuarioid=auth::user();
            $localidadid=auth::local();

            $sqlmotivo="select id,nombre from motivoingreso where activo=1 order by nombre";
            $sql=" select id,motivoingresoid,comentario
             from ".$this->table." 
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";

             $sqldet=" select id,productoid,descripcion,cantidad
             from ".$this->table."_detalle
             where localidadid='$localidadid' 
             and usuario_creacion='$usuarioid'
             and activo=1";
            
             $cab= new ENotaingresotemp($this->sqlgetrow($sql));
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
                $sqltable="insert into notaingresotemp(id,localidadid,motivoingresoid,comentario,activo,usuario_creacion,fecha_hora_creacion)
                values('$o->id','$localidadid','$o->motivoingresoid','$o->comentario',1,'$usuarioid',".$hoy.")";
             }
             else{
                $sqltable="update notaingresotemp
                set motivoingresoid='$o->motivoingresoid',comentario='$o->comentario'
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
            $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
            from producto 
             where activo=1 
             and not id in (select productoid from ".$this->table."_detalle
             where localidadid='$localidadid' and usuario_creacion='$usuarioid')
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
            $sql=" select a.id,a.motivoingresoid,a.comentario,b.nombre as motivoingreso_nombre,
            (SELECT ifnull(max(cast(numero AS SIGNED INTEGER)),0)+1 from notaingreso) as numero
             from ".$this->table." as a 
             inner join motivoingreso as b on b.id=a.motivoingresoid
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
             insert into notaingreso(id,numero,localidadid,motivoingresoid,comentario,activo,usuario_creacion,fecha_hora_creacion)
             select '$id','".$cab["numero"]."','$localidadid','".$cab["motivoingresoid"]."','".$cab["comentario"]."',1,'$usuarioid',$hoy ";

             $array = array($sql);
                $correlativo=1;
             foreach($dtdet as $det){
                $sql="insert into notaingreso_detalle(id,correlativo,notaingresoid,localidadid,productoid,descripcion,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),$correlativo,'$id','$localidadid','".$det["productoid"]."','".$det["descripcion"]."','".$det["cantidad"]."',1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $productoid=$det["productoid"];
                if($det["locprod"]=="N"){
                    $sql="insert into localidad_producto(id,localidadid,productoid,cantidad,activo,usuario_creacion,fecha_hora_creacion)
                    values(uuid(),'$localidadid','$productoid',0,1,'$usuarioid',$hoy)";
                    array_push($array,$sql);
                }

                $cantidad=$det["cantidad"];
                $nuevo_saldo=$det["stock_actual"] + $cantidad;
                $tipo="ING";
                $descripcion = "NI ".$cab["numero"]." - INGRESO POR: ".$cab["motivoingreso_nombre"];
                $sql="insert into localidad_producto_detalle(id,localidadid,productoid,descripcion,tipo,cantidad,saldo,activo,usuario_creacion,fecha_hora_creacion)
                values(uuid(),'$localidadid','$productoid','$descripcion','$tipo', $cantidad ,$nuevo_saldo,1,'$usuarioid',$hoy)";
                array_push($array,$sql);

                $sql="update localidad_producto set
                cantidad=$nuevo_saldo , usuario_modificacion='$usuarioid', fecha_hora_modificacion=$hoy
                where localidadid='$localidadid' and productoid='$productoid' ";
                array_push($array,$sql);
                $correlativo++;
             }
            $sql="delete from notaingresotemp_detalle
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

            $sql="delete from notaingresotemp
            where localidadid='$localidadid' and usuario_creacion='$usuarioid' ";
            array_push($array,$sql);

             $this->db->transacm($array,"Se generó una Nota de Ingreso N° ".$cab["numero"]);
        }
        function validarFinalizar($dtcab,$dtdet){
            $details = array();
            if(count($dtcab)==0){
                array_push($details,"No hay una nota de ingreso pendiente para grabar");
            }
            else{
                $motivoingresoid=$dtcab[0]["motivoingresoid"];
                if(!isGuid($motivoingresoid)){
                    array_push($details,"Debe seleccionar un motivo de ingreso");
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