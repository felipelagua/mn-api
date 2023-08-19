<?php
    class Dtraslado extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS localidaddestino_nombre,
            a.comentario,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre as solicitadopor_nombre,
            (select count(1) from traslado_detalle where trasladoid=a.id) as items
            FROM traslado AS a
            INNER JOIN localidad AS b ON b.id=a.localidaddestinoid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            INNER JOIN usuario AS e ON e.id=a.solicitadoporid
            WHERE a.localidadid='$localidadid'";
            if($o->tipo=="M"){
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y') = '$o->anio'
                and date_format(a.fecha_hora_creacion,'%m') = '$o->mes'";
            }
            else{
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y-%m-%d') between  '$o->desde' and  '$o->hasta'";
            }
            $sql.=" and ('$o->localidaddestinoid'='X' or a.localidaddestinoid='$o->localidaddestinoid')";
            $sql.=" and ('$o->usuariocreador'='X' or a.usuario_creacion='$o->usuariocreador')";
            $sql.=" and ('$o->solicitadoporid'='X' or a.solicitadoporid='$o->solicitadoporid')";
            $sql.=" and ('$o->numero'='' or a.numero='$o->numero')";
            $sql.=" order by a.fecha_hora_creacion desc";
           
            $this->sqlread($sql);
             
        }
        public function obtener($o){
            $sql=" SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS localidad_nombre,
            c.nombre AS localidaddestino_nombre,
            a.comentario,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre,
            e.nombre AS solicitadopor_nombre
            FROM traslado AS a
            INNER JOIN localidad AS b ON b.id=a.localidadid
            INNER JOIN localidad AS c ON c.id=a.localidaddestinoid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            INNER JOIN usuario as e on e.id=a.solicitadoporid
            WHERE a.id='$o->id'";
           
            $sqldet="SELECT descripcion,cantidad
            FROM traslado_detalle
            where trasladoid='$o->id'
            ORDER BY fecha_hora_creacion";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function obtenerFiltros(){
            $hoy=now();
            $sqlmotivo =" SELECT id,nombre from motivosalida where  activo=1 order by nombre"; 
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format($hoy,'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM traslado";
            $sqlusuario="SELECT id,nombre FROM usuario WHERE id IN (SELECT usuario_creacion FROM traslado WHERE activo=1) AND activo=1";
            $sqlsolicitante="SELECT id,nombre FROM usuario WHERE id IN (SELECT solicitadoporid FROM traslado WHERE activo=1) AND activo=1";
            $sqlanioactual="SELECT date_format($hoy,'%Y') AS id,date_format($hoy,'%Y') as nombre";

            $sqllocalidaddestino="SELECT id,nombre FROM localidad WHERE id IN (SELECT localidaddestinoid FROM traslado WHERE activo=1) AND activo=1";

            $anios=$this->sqldata($sqlanio);
            $anioactual=$this->sqlgetrow($sqlanioactual);
            if(count($anios)==0){
                $anios[0]=$anioactual;
            }
            $data["motivos"]=$this->sqldata($sqlmotivo);
            $data["usuarios"]=$this->sqldata($sqlusuario);
            $data["meses"]=$this->sqldata($sqlmes);
            $data["anios"]=$anios;
            $data["mesactual"]=$this->sqlgetrow($sqlmesactual)["mes"] ;
            $data["anioactual"]= $anioactual["id"] ;
            $data["personas"]=$this->sqldata($sqlusuario);
            $data["solicitante"]=$this->sqldata($sqlsolicitante);
            $data["localidaddestino"]=$this->sqldata($sqllocalidaddestino);
            $this->gotoSuccessData($data); 
 
        }
    }
?>