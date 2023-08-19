<?php
class reporteproducto extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DReporteProducto");
    }

    public function listar(){
        http::role(REPORTE_PRODUCTO);
        http::post();
        $d=new DReporteProducto();
        $data = $d->listar();
        $pdf = new PDF();
        $pdf->setTitle("PRODUCTOS");
       
        $head = array('PRODUCTO','CLASIFIC 1','CLASIFIC 2','CLASIFIC 3','TERM','STOCK','INST','COMP','VENTA');
        $align=array('L','L','L','L','C','C','C','C','C');
        $field = array('nombre','clasificacion1_nombre','clasificacion2_nombre','clasificacion3_nombre','terminado','stock','instantaneo','compra','venta');
        $width= array(60,30,30,20,10,10,10,10,10);
        $pdf->setTable($data,$head,$field,$align,$width);
        $pdf->download("reporte_producto");
    }
    public function listarventa(){
        http::role(REPORTE_PRODUCTO);
        http::post();
        $d=new DReporteProducto();
        $data = $d->listarventa();
        $pdf = new PDF();
        $pdf->setTitle("PRODUCTOS DE VENTA");
       
        $head = array('PRODUCTO','CLASIFIC 1','CLASIFIC 2','CLASIFIC 3','TERM','STOCK','INST','COMP','PREC. VTA');
        $align=array('L','L','L','L','C','C','C','C','R');
        $field = array('nombre','clasificacion1_nombre','clasificacion2_nombre','clasificacion3_nombre','terminado','stock','instantaneo','compra','precio_venta');
        $width= array(60,30,20,20,10,10,10,10,20);
        $pdf->setTable($data,$head,$field,$align,$width);
        $pdf->download("reporte_producto_venta");
    }
    public function listarinstantaneo(){
        http::role(REPORTE_PRODUCTO);
        http::post();
        $d=new DReporteProducto();
        $data = $d->listarinstantaneo();
        $pdf = new PDF();
        $pdf->setTitle("PRODUCTOS INSTANTANEOS");
       
        $head = array('PRODUCTO','CLASIFIC 1','CLASIFIC 2','CLASIFIC 3','STOCK','COMP','VENTA','PREC. VTA');
        $align=array('L','L','L','L','C','C','C','R');
        $field = array('nombre','clasificacion1_nombre','clasificacion2_nombre','clasificacion3_nombre','stock','compra','venta','precio_venta');
        $width= array(60,30,30,20,10,10,10,20);
        $pdf->setTable($data,$head,$field,$align,$width);
        $pdf->download("reporte_producto_instantaneo");
    }
    public function listarterminado(){
        http::role(REPORTE_PRODUCTO);
        http::post();
        $d=new DReporteProducto();
        $data = $d->listarterminado();
        $pdf = new PDF();
        $pdf->setTitle("PRODUCTOS TERMINADOS");
       
        $head = array('PRODUCTO','CLASIFIC 1','CLASIFIC 2','CLASIFIC 3','STOCK','COMP','VENTA','PREC. VTA');
        $align=array('L','L','L','L','C','C','C','R');
        $field = array('nombre','clasificacion1_nombre','clasificacion2_nombre','clasificacion3_nombre','stock','compra','venta','precio_venta');
        $width= array(60,30,30,20,10,10,10,20);
        $pdf->setTable($data,$head,$field,$align,$width);
        $pdf->download("reporte_producto_instantaneo");
    }
    public function listarProductoConInsumo(){
        http::role(REPORTE_PRODUCTO);
        http::post();
        $d=new DReporteProducto();
        $data = $d->listarProductoConInsumo();
        $pdf = new PDF();
        $pdf->setTitle("PRODUCTOS CON INSUMO");
       
        $head = array('PRODUCTO');
        $align=array('L');
        $field = array('nombre');
        $width= array(190);
        $pdf->setTable($data,$head,$field,$align,$width);
        $pdf->download("reporte_productos_con_insumo");
    }
}

?>