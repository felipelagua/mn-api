<?php
    class DVenta extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            concat(b.nombre_corto,' ',a.numero) as numero,e.nombre AS cliente_nombre,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre as cliente_nombre,
            a.total,a.pago,a.saldo
            FROM venta AS a
            LEFT JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            inner join cliente as e on e.id=a.clienteid 
             WHERE a.localidadid='$localidadid'";
          
            if($o->tipo=="M"){
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y') = '$o->anio'
                and date_format(a.fecha_hora_creacion,'%m') = '$o->mes'";
            }
            else{
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y-%m-%d') between  '$o->desde' and  '$o->hasta'";
            }
            $sql.=" and ('$o->usuariocreador'='X' or a.usuario_creacion='$o->usuariocreador')";
            $sql.=" and ('$o->numero'='' or a.numero='$o->numero')";
            $sql.=" order by a.fecha_hora_creacion desc";
             
           
            $this->sqlread($sql);
             
        }
        public function obtener($o){
            $sql=" SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS tipocomprobante_nombre,
            a.total,a.saldo,a.pago,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre as cliente_nombre
            FROM venta AS a
            LEFT JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            INNER JOIN cliente AS e ON e.id=a.clienteid
            WHERE a.id='$o->id'";
           
            $sqldet="SELECT descripcion,cantidad,precio,importe
            FROM venta_detalle
            where ventaid='$o->id'
            and activo=1
            ORDER BY correlativo,fecha_hora_creacion";

            $sqlpag="SELECT descripcion,pago
            FROM venta_pago
            where ventaid='$o->id'
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
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM venta";
            $sqlusuario="SELECT id,nombre FROM usuario WHERE id IN (SELECT usuario_creacion FROM venta WHERE activo=1) AND activo=1";
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
    }
?>