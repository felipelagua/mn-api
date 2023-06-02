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
}
?>