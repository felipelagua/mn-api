<?php
class Pedidocompratemp extends Controller{
    public function __construct(){
        parent::__construct();
 
        parent::usingEntity("EPedidocompratemp");
        parent::usingEntity("EPedidocompraDetalle");
        parent::usingData("DPedidocompratemp");
        parent::usingValidate("VPedidocompraDetalle");
        parent::usingEntity("EFiltro");
    }

    public function registrar(){
        http::role(NUEVO_INGRESO);
        http::put();
        $input=input();
        $o=new EPedidocompratemp($input);
        $d=new DPedidocompratemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(NUEVO_INGRESO);
        http::post();
        $d=new DPedidocompratemp();
        $d->obtener();
    }
    public function listarDetalle(){
        http::role(NUEVO_INGRESO);
        http::post();
        $d=new DPedidocompratemp();
        $d->listarDetalle();
    }
    public function buscarProducto(){
        http::role(NUEVO_INGRESO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompratemp();
        $d->buscarProducto($o);
    }
    public function registrarDetalle(){
        http::role(NUEVO_INGRESO);
        http::put();
        $input=input();
        $o=new EPedidocompraDetalle($input);
        $v=new VPedidocompraDetalle();
        $v->validate($o);
        $d=new DPedidocompratemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(NUEVO_INGRESO);
        http::delete();
        $input=input();
        $o=new EPedidocompraDetalle($input); 
        $d=new DPedidocompratemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(NUEVO_INGRESO);
        http::put();
        $d=new DPedidocompratemp();
        $d->finalizar();
    }
}

?>