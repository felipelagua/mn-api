<?php
class compra extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DCompra");
        parent::usingData("DCompratemp");
        parent::usingEntity("EFiltro");
        parent::usingEntity("ECompraDetalle");
        parent::usingEntity("ECompraPago");
        parent::usingEntity("ECuentaDetalle");
    }

    public function listar(){
        http::role(COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DCompra();
        $d->listar($o);
    }
    public function listarPendiente(){
        http::role(PAGO_PROVEEDOR);http::post();
        $o=new EFiltro(input());
        $d=new DCompra();
        $d->listarPendiente($o);
    }
    public function obtenerPendiente(){
        http::role(PAGO_PROVEEDOR);http::post();
        $o=new EFiltro(input());
        $d=new DCompra();
        $d->obtener($o);
    }
    public function listarPagoCuentaPendiente(){
        http::role(PAGO_PROVEEDOR);http::post();
        $d=new DCompratemp();
        $d->listarPagoCuenta();
    }
    public function listarPagoCuenta(){
        http::role(COMPRA_REGISTRO);http::post();
        $d=new DCompratemp();
        $d->listarPagoCuenta();
    }
    public function registrarPago(){
        http::role(COMPRA_REGISTRO);http::put();
        $input=input();
        $o=new ECompraPago($input);
        $d=new DCompra();
        $d->registrarPago($o);
    }
    public function obtener(){
        http::role(COMPRA);http::post();
        $o=new EFiltro(input());
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