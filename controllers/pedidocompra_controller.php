<?php
class pedidocompra extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DPedidocompra");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(PEDIDO_COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompra();
        $d->listar($o);
    }
    public function obtener(){
        http::role(PEDIDO_COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompra();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(PEDIDO_COMPRA);
        http::post();
        $d=new DPedidocompra();
        $d->obtenerFiltros();
    }
}
?>