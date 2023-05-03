<?php
class compra extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DCompra");
        parent::usingEntity("EFiltro");
        parent::usingEntity("ECompraDetalle");
    }

    public function listar(){
        http::role(COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DCompra();
        $d->listar($o);
    }
    public function obtener(){
        http::role(COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DCompra();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(COMPRA);
        http::post();
        $d=new DCompra();
        $d->obtenerFiltros();
    }
    public function listarDestinoPendiente(){
        http::role(COMPRA_DESTINO);
        http::post();
        $d=new DCompra();
        $d->listarDestinoPendiente();
    }
    public function actualizarDestino(){
        http::role(COMPRA_DESTINO);
        http::put();
        $input=input();
        $o=new ECompraDetalle($input);
        $d=new DCompra();
        $d->actualizarDestino($o);
    }
}
?>