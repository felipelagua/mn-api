<?php
class venta extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DVenta");
        parent::usingEntity("EFiltro"); 
    }

    public function listar(){
        http::role(VENTA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DVenta();
        $d->listar($o);
    }
    public function obtener(){
        http::role(VENTA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DVenta();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(VENTA);
        http::post();
        $d=new DVenta();
        $d->obtenerFiltros();
    }
    public function reportediatotal(){
        http::role(REPORTE_VENTA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DVenta();
        $d->reportediatotal($o);
    }
    public function reportediacount(){
        http::role(REPORTE_VENTA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DVenta();
        $d->reportediacount($o);
    }
    public function reportediahora(){
        http::role(REPORTE_VENTA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DVenta();
        $d->reportediahora($o);
    }
    public function productovendido(){
        http::role(REPORTE_VENTA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DVenta();
        $d->productovendido($o);
    }
}
?>