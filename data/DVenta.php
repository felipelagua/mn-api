<?php
usingdb("venta");
    class DVenta extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,ifnull(e.nombre,'CLIENTE GENERICO') AS cliente_nombre,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,'S/' AS moneda_simbolo,
            a.total,a.pago,a.saldo,f.numero as pedido_numero
            FROM venta AS a
            LEFT JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            LEFT join cliente as e on e.id=a.clienteid
            left join pedido as f on f.id=a.pedidoid
             WHERE a.localidadid='$localidadid'";
          
            if($o->tipo=="M"){
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y') = '$o->anio'
                and date_format(a.fecha_hora_creacion,'%m') = '$o->mes'";
            }
            else{
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y-%m-%d') between  '$o->desde' and  '$o->hasta'";
            }
            $sql.=" and ('$o->usuariocreador'='X' or a.usuario_creacion='$o->usuariocreador')";
            $sql.=" and ('$o->nombre'='' or a.numero='$o->nombre')";
            $sql.=" order by a.fecha_hora_creacion desc";
             
           
            $this->sqlread($sql);
             
        }
        public function obtener($o){
            $sql=" SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS tipocomprobante_nombre,
            a.total,a.saldo,a.pago,
             c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            ifnull(e.nombre,'CLIENTE GENERICO') as cliente_nombre,
            ifnull(f.numero,'') as pedido_numero
            FROM venta AS a
            LEFT JOIN tipocomprobante AS b ON b.id=a.tipocomprobanteid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            left JOIN cliente AS e ON e.id=a.clienteid
            left join pedido as f on f.id=a.pedidoid
            WHERE a.id='$o->id'";
           
            $sqldet="SELECT descripcion,cantidad,precio,importe
            FROM venta_detalle
            where ventaid='$o->id'
            and activo=1
            ORDER BY correlativo,fecha_hora_creacion";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $data["pago"]=$this->sqldata(db_venta_pago_listar($o->id));
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
        public function reportediatotal($o){
            $data["loc"]=$this->sqldata(db_venta_localidad_mes_listar($o->anio,$o->mes) );
            $data["vta"]=$this->sqldata(db_venta_mes_total_listar($o->anio,$o->mes) );           
           $this->gotoSuccessData($data);         
        }
        public function reportediacount($o){
            $data["loc"]=$this->sqldata(db_venta_localidad_mes_listar($o->anio,$o->mes) );
            $data["vta"]=$this->sqldata(db_venta_mes_count_listar($o->anio,$o->mes) );           
           $this->gotoSuccessData($data);         
        }
        public function reportediahora($o){
            $data["loc"]=$this->sqldata(db_venta_localidad_mes_listar($o->anio,$o->mes) );
            $data["vta"]=$this->sqldata(db_venta_mes_hora_listar($o->anio,$o->mes) );           
           $this->gotoSuccessData($data);         
        }
        public function productovendido($o){
            $data["loc"]=$this->sqldata(db_venta_localidad_mes_listar($o->anio,$o->mes) );
            $data["vta"]=$this->sqldata(db_venta_producto_listar($o->anio,$o->mes) );           
           $this->gotoSuccessData($data);         
        }
    }
?>