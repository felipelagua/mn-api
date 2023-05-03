<?php
    class DProducto extends Model{
        private $table="producto";
        public function registrar($o){
            if($o->clasificacion1id=="X"){ $o->clasificacion1id="";}
            if($o->clasificacion2id=="X"){ $o->clasificacion2id="";}
            if($o->clasificacion3id=="X"){ $o->clasificacion3id="";}
            $this->save($this->table,$o);
        }
        public function eliminar($o){
            $this->delete($this->table,$o);
        }
        public function listar($o){
            $sql=" select id,nombre,stock from producto where activo=1 and nombre like  '%".$o->nombre."%'";
            $this->sqlread($sql);
        }
        public function obtener($o){
            $sql=" select id,nombre,stock,compra,venta,instantaneo,terminado,
            case when clasificacion1id='' then 'X' else clasificacion1id end as clasificacion1id,
            case when clasificacion2id='' then 'X' else clasificacion2id end as clasificacion2id,
            case when clasificacion3id='' then 'X' else clasificacion3id end as clasificacion3id,
            precio_compra,precio_venta
            from producto where id='$o->id' and activo=1";

            $sqlclasificacion1="select id,nombre
            from clasificacion1 where activo=1 
            order by nombre";

            $sqlclasificacion2="select id,nombre
            from clasificacion2 where activo=1 
            order by nombre";

            $sqlclasificacion3="select id,nombre
            from clasificacion3 where activo=1 
            order by nombre";

            $data["ent"]=$this->sqlgetrow($sql);
            $data["clasificacion1"]=$this->sqldata($sqlclasificacion1);
            $data["clasificacion2"]=$this->sqldata($sqlclasificacion2);
            $data["clasificacion3"]=$this->sqldata($sqlclasificacion3);
            $data["destino"]=$this->sqldata($this->sqlDestino($o));
            $data["insumo"]=$this->sqldata($this->sqlInsumo($o));
            $this->gotoSuccessData($data);
        }
        public function obtenerListas(){

            $sqlclasificacion1="select id,nombre
            from clasificacion1 where activo=1 
            order by nombre";

            $sqlclasificacion2="select id,nombre
            from clasificacion2 where activo=1 
            order by nombre";

            $sqlclasificacion3="select id,nombre
            from clasificacion3 where activo=1 
            order by nombre";

            $data["clasificacion1"]=$this->sqldata($sqlclasificacion1);
            $data["clasificacion2"]=$this->sqldata($sqlclasificacion2);
            $data["clasificacion3"]=$this->sqldata($sqlclasificacion3);
            $this->gotoSuccessData($data);
        }

        public function buscarProducto($o){
            $sql="";
            if($o->tipo=="D"){
                $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
                from producto 
                 where activo=1 
                 and terminado='NO'
                 and instantaneo='NO'
                 and stock='NO'
                 and nombre like  '%".$o->nombre."%'
                 order by nombre asc";
            }
            else{
                $sql=" select id as productoid,nombre as descripcion,1 as cantidad 
                from producto 
                 where activo=1 
                 and terminado='NO'
                 and instantaneo='NO'
                 and venta='NO'
                 and nombre like  '%".$o->nombre."%'
                 order by nombre asc";
            }
           
              $this->sqlread($sql);
        }
        public function listarDestino($o){
             $sqldet=$this->sqlDestino($o);

            $data["destino"]=$this->sqldata($sqldet);
            $this->gotoSuccessData($data); 
        }
        private function sqlDestino($o){
            $sql=" SELECT a.id,b.nombre,a.cantidad,
            case when a.cantidad=0 then'Definir la cantidad luego de la compra de origen'
            else 'Cantidad automatica generada al comprar el origen'
            end as descripcion
             FROM producto_destino AS a 
             INNER JOIN producto AS b ON b.id=a.itemid
             WHERE a.productoid = '$o->id' and a.activo = 1";
             return $sql;
        }
        public function registrarDestino($o){
            $usuarioid=auth::user();
            $hoy=now();
            $o->id = guid();
            if(!$this->existeDestino($o)){
                $sql="insert into producto_destino(id,productoid,itemid,cantidad,activo,usuario_creacion,fecha_hora_creacion) 
                values('$o->id','$o->productoid','$o->itemid','$o->cantidad',1,'$usuarioid',$hoy)";
                $this->db->execute($sql);
                $this->gotoSuccess("Se actualizaron los datos con éxito",$o->id);
            }
            else{
                $this->gotoError("El destino ya fue agregado a la lista");
            }
        }
        private function existeDestino($o){
            $state=false;
            $sql=" select id from producto_destino
            where productoid='$o->productoid' and itemid='$o->itemid'
            and activo = 1 ";
            $dt=$this->sqldata($sql);
            if(count($dt)>0){
                $state=true;
            }
            return $state;
        }
        public function eliminarDestino($o){
            $usuarioid=auth::user();
            $hoy=now();
             $sqldet=" update producto_destino
             set activo=0,
             usuario_modificacion='$usuarioid',
             fecha_hora_modificacion= $hoy
             where id='$o->id'  ";

             $this->db->execute($sqldet);
            $this->gotoSuccess("Se eliminó correctamente",$o->id); 
        }

        public function listarInsumo($o){
            $sqldet=$this->sqlInsumo($o);

           $data["insumo"]=$this->sqldata($sqldet);
           $this->gotoSuccessData($data); 
       }
       private function sqlInsumo($o){
           $sql=" SELECT a.id,b.nombre,a.cantidad,
           case when a.cantidad=0 then'Definir la cantidad luego de la compra de origen'
           else 'Cantidad automatica generada al comprar el origen'
           end as descripcion
            FROM producto_Insumo AS a 
            INNER JOIN producto AS b ON b.id=a.itemid
            WHERE a.productoid = '$o->id' and a.activo = 1";
            return $sql;
       }
       public function registrarInsumo($o){
           $usuarioid=auth::user();
           $hoy=now();
           $o->id = guid();
           if(!$this->existeInsumo($o)){
               $sql="insert into producto_insumo(id,productoid,itemid,cantidad,activo,usuario_creacion,fecha_hora_creacion) 
               values('$o->id','$o->productoid','$o->itemid','$o->cantidad',1,'$usuarioid',$hoy)";
               $this->db->execute($sql);
               $this->gotoSuccess("Se actualizaron los datos con éxito",$o->id);
           }
           else{
               $this->gotoError("El inusmo ya fue agregado a la lista");
           }
       }
       private function existeInsumo($o){
           $state=false;
           $sql=" select id from producto_insumo
           where productoid='$o->productoid' and itemid='$o->itemid'
           and activo = 1 ";
           $dt=$this->sqldata($sql);
           if(count($dt)>0){
               $state=true;
           }
           return $state;
       }
       public function eliminarInsumo($o){
           $usuarioid=auth::user();
           $hoy=now();
            $sqldet=" update producto_insumo
            set activo=0,
            usuario_modificacion='$usuarioid',
            fecha_hora_modificacion= $hoy
            where id='$o->id'  ";

            $this->db->execute($sqldet);
           $this->gotoSuccess("Se eliminó correctamente",$o->id); 
       }

    }
?>