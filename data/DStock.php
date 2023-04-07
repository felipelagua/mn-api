<?php
    class DStock extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="SELECT b.id,b.nombre,convert(a.cantidad,decimal(10,0)) as cantidad
            FROM localidad_producto AS a
            INNER JOIN producto AS b ON b.id=a.productoid
            WHERE a.localidadid = '$localidadid'
            AND a.activo=1
            AND b.activo=1
            and b.nombre like  '%".$o->nombre."%'
            ORDER BY b.nombre";

            $this->sqlread($sql);
        }
        public function obtener($o){
            $localidadid=auth::local();

            $sql=  $sql="SELECT b.id,b.nombre,convert(a.cantidad,decimal(10,0)) as cantidad
            FROM localidad_producto AS a
            INNER JOIN producto AS b ON b.id=a.productoid
            WHERE a.localidadid = '$localidadid'
            AND a.activo=1
            AND b.activo=1
            and b.id =  '$o->id'";

            $sqldet="SELECT 
            DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
            a.descripcion,a.tipo,convert(a.cantidad , decimal(10,0)) as cantidad,convert(a.saldo , decimal(10,0)) as saldo
            FROM localidad_producto_detalle AS a
            WHERE a.productoid='$o->id'
            and a.localidadid='$localidadid'
            ORDER BY a.fecha_hora_creacion DESC
            LIMIT 0,100";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data);
        }
    }
?>