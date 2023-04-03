<?php
    class DNotaingreso extends Model{
 
        public function listar($o){
            $localidadid=auth::local();
            $sql="
            SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS motivoingreso_nombre,
            a.comentario,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre
            FROM notaingreso AS a
            INNER JOIN motivoingreso AS b ON b.id=a.motivoingresoid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            WHERE a.localidadid='$localidadid'";
            if($o->tipo=="M"){
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y') = '$o->anio'
                and date_format(a.fecha_hora_creacion,'%m') = '$o->mes'";
            }
            else{
                $sql.=" and date_format(a.fecha_hora_creacion,'%Y-%m-%d') between  '$o->desde' and  '$o->hasta'";
            }
            $sql.=" and ('$o->motivo'='X' or a.motivoingresoid='$o->motivo')";
            $sql.=" and ('$o->usuariocreador'='X' or a.usuario_creacion='$o->usuariocreador')";
            $sql.=" and ('$o->numero'='' or a.numero='$o->numero')";
            $sql.=" order by a.fecha_hora_creacion desc";
           
            $this->sqlread($sql);
             
        }
        public function obtener($o){
            $sql=" SELECT a.id, date_format(a.fecha_hora_creacion,'%d/%m/%Y %H:%i') as fecha_hora_creacion,
            a.numero,b.nombre AS motivoingreso_nombre,
            a.comentario,
            c.nombre AS localidad_nombre,
            d.nombre AS usuario_nombre
            FROM notaingreso AS a
            INNER JOIN motivoingreso AS b ON b.id=a.motivoingresoid
            INNER JOIN localidad AS c ON c.id=a.localidadid
            INNER JOIN usuario AS d ON d.id=a.usuario_creacion
            WHERE a.id='$o->id'";
           
            $sqldet="SELECT descripcion,cantidad
            FROM notaingreso_detalle
            where notaingresoid='$o->id'
            ORDER BY fecha_hora_creacion";

            $data["cabecera"]=$this->sqlgetrow($sql);
            $data["detalle"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        public function obtenerFiltros(){
            $sqlmotivo =" SELECT id,nombre from motivoingreso where  activo=1 order by nombre"; 
            $sqlmes =" SELECT id,nombre from mes order by id";
            $sqlmesactual="SELECT date_format(DATE_ADD(NOW(), INTERVAL 2 HOUR),'%m') AS mes";
            $sqlanio="SELECT distinct DATE_FORMAT(fecha_hora_creacion,'%Y') AS id,DATE_FORMAT(fecha_hora_creacion,'%Y') AS nombre FROM notaingreso";
            $sqlusuario="SELECT id,nombre FROM usuario WHERE id IN (SELECT usuario_creacion FROM notaingreso WHERE activo=1) AND activo=1";

            $data["motivos"]=$this->sqldata($sqlmotivo);
            $data["usuarios"]=$this->sqldata($sqlusuario);
            $data["meses"]=$this->sqldata($sqlmes);
            $data["anios"]=$this->sqldata($sqlanio);
            $data["mesactual"]=$this->sqlgetrow($sqlmesactual)["mes"] ;
            $data["anioactual"]=$data["anios"][0]["id"] ;
            $data["personas"]=$this->sqldata($sqlusuario);
            $this->gotoSuccessData($data); 
 
        }
    }
?>