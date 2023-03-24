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
                where localidadid='$localidadid' and productoid='$o->productoid' and usuario_creacion='$usuarioid'";
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
    }
?>