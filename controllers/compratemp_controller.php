<?php
class compratemp extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivoingreso");
        parent::usingEntity("EMotivoingreso");
        parent::usingEntity("ECompratemp");
        parent::usingEntity("ECompraDetalle");
        parent::usingEntity("ECajaDetalle");
        parent::usingEntity("ECuentaDetalle");
        parent::usingEntity("ECompraPago");
        parent::usingData("DCompratemp");
        parent::usingValidate("VMotivoingreso");
        parent::usingValidate("VCompraDetalle");
        parent::usingValidate("VCompraPago");
        parent::usingEntity("EFiltro");
    }

    public function registrar(){
        http::role(COMPRA_REGISTRO);
        http::put();
        $input=input();
        $o=new ECompratemp($input);
        $d=new DCompratemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $d=new DCompratemp();
        $d->obtener();
    }
    public function listarDetalle(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $d=new DCompratemp();
        $d->listarDetalle();
    }
    public function buscarProducto(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DCompratemp();
        $d->buscarProducto($o);
    }
    public function buscarProveedor(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DCompratemp();
        $d->buscarProveedor($o);
    }
    public function registrarDetalle(){
        http::role(COMPRA_REGISTRO);
        http::put();
        $input=input();
        $o=new ECompraDetalle($input);
        $v=new VCompraDetalle();
        $v->validate($o);
        $d=new DCompratemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(COMPRA_REGISTRO);
        http::delete();
        $input=input();
        $o=new ECompraDetalle($input); 
        $d=new DCompratemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(COMPRA_REGISTRO);
        http::put();
        $d=new DCompratemp();
        $d->finalizar();
    }

    public function listarPagoCuenta(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $d=new DCompratemp();
        $d->listarPagoCuenta();
    }
    public function registrarPago(){
        http::role(COMPRA_REGISTRO);
        http::put();
        $input=input();
        $o=new ECompraPago($input);
        $v=new VCompraPago();
        $v->validate($o);
        $d=new DCompratemp();
        $d->registrarPago($o);
    }
    public function eliminarPago(){
        http::role(COMPRA_REGISTRO);
        http::delete();
        $input=input();
        $o=new ECompraDetalle($input); 
        $d=new DCompratemp();
        $d->eliminarPago($o);
    }
    public function listarPago(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $d=new DCompratemp();
        $d->listarPago();
    }
}

?>