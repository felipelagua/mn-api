<?php
usingdb("producto");
    class DReporteProducto extends Model{
 
        public function listar(){           
            $sql=db_reporte_producto_listar();
            return $this->sqldata($sql);
        }
        public function listarventa(){           
            $sql=db_reporte_producto_venta_listar();
            return $this->sqldata($sql);
        }
        public function listarinstantaneo(){           
            $sql=db_reporte_producto_instantaneo_listar();
            return $this->sqldata($sql);
        }
        public function listarterminado(){           
            $sql=db_reporte_producto_terminado_listar();
            return $this->sqldata($sql);
        }
    }
?>