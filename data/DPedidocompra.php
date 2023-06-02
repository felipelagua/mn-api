<?php
    class DPedidocompra extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre
            FROM pedidocompra AS a
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            WHERE a.localidadid='$localidadid' and a.activo=1";
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
            a.numero,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre
            FROM pedidocompra AS a
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            WHERE a.id='$o->id'";
           
            $sqldet="SELECT descripcion,cantidad
            FROM pedidocompra_detalle
            where pedidocompraid='$o->id'
            ORDER BY fecha_hora_creacion";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function obtenerFiltros(){
            $hoy=now();
          
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format($hoy,'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM Pedidocompra";
            $sqlusuario="SELECT id,nombre FROM usuario WHERE id IN (SELECT usuario_creacion FROM Pedidocompra WHERE activo=1) AND activo=1";
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
        public function listarRegistrado(){
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            a.toma AS activado
            FROM pedidocompra AS a
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            WHERE a.estado='REG' and a.activo=1
            order by a.fecha_hora_creacion desc";
           
            $this->sqlread($sql);   
        }
        public function tomarPedido($o){
            $sql=" update pedidocompra
            set toma='$o->activado'
            where id='$o->id' 
            and activo=1";
            $this->db->execute($sql);
            $this->gotoSuccess("Se marcó el pedido compra correctamente",$o->id);
        }
        public function listarDetallePedidoTomado($estado){
            $sql="SELECT c.id,c.nombre,SUM(a.cantidad) AS cantidad
            FROM pedidocompra_detalle AS a
            INNER JOIN pedidocompra AS b ON b.id=a.pedidocompraid
            INNER JOIN producto AS c ON c.id=a.productoid
            WHERE a.activo=1 AND b.estado='$estado' AND b.toma='SI'
            AND b.activo=1
            GROUP BY c.id,c.nombre
            ORDER BY c.nombre";          
            $data = $this->sqldata($sql); 
            return $data;
        }
        public function listarPedidoTomado($estado){
            $sql="SELECT a.id,a.numero,b.nombre AS localidad_nombre,
            c.nombre AS usuario_nombre,
            DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
            case   a.estado 
                WHEN 'REG' then 'REGOSTRADO' 
                WHEN 'APR' then 'APROBADO' 
                WHEN 'CMP' then 'COMPRADO' 
                WHEN 'ATE' then 'ATENDIDO' 
            else '' end as estado_nombre
            FROM pedidocompra AS a
            INNER JOIN localidad AS b ON b.id=a.localidadid
            INNER JOIN usuario as c ON c.id=a.usuario_creacion
            WHERE a.estado='$estado' and a.toma='SI'
            AND a.activo=1
            ORDER BY a.fecha_hora_creacion,numero";          
            $data = $this->sqldata($sql); 
            return $data;
        }
        public function listarPedidoCompra($o){
            $sql="SELECT a.id,a.numero,b.nombre AS localidad_nombre,
            c.nombre AS usuario_nombre,
            DATE_FORMAT(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') AS fecha_hora,
            case   a.estado 
                WHEN 'REG' then 'REGOSTRADO' 
                WHEN 'APR' then 'APROBADO' 
                WHEN 'CMP' then 'COMPRADO' 
                WHEN 'ATE' then 'ATENDIDO' 
            else '' end as estado_nombre
            FROM pedidocompra AS a
            INNER JOIN localidad AS b ON b.id=a.localidadid
            INNER JOIN usuario as c ON c.id=a.usuario_creacion
            WHERE a.localidadid='$o->localidaddestinoid' and a.estado='CMP' and a.toma='SI'
            AND a.activo=1
            ORDER BY a.fecha_hora_creacion,numero";          
            $data = $this->sqlread($sql); 
            
        }
        public function listarDetalle($id){
            $sql="SELECT descripcion as nombre,cantidad,correlativo
            FROM pedidocompra_detalle
            where pedidocompraid='$id'
            and activo=1
            ORDER BY correlativo";
            $data  = $this->sqldata($sql); 
            return $data;
        }

        public function aprobar(){
            $usuarioid=auth::user(); 
            $hoy=now();

            $dt=$this->listarDetallePedidoTomado('REG');
            if(count($dt)==0){
                $this->gotoError("No se ha tomado ningun pedido para aprobar");
            }
            $sql=" update pedidocompra
            set estado='APR',
            usuario_modificacion='$usuarioid',
            fecha_hora_modificacion=$hoy
            where toma='SI' and estado='REG'
            and activo=1";
            $this->db->execute($sql);
            $this->gotoSuccess("Se marcó el pedido compra correctamente","");
        }
    }
?>