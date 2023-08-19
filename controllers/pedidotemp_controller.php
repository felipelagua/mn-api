<?php
class pedidotemp extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivoingreso");
        parent::usingEntity("EMotivoingreso");
        parent::usingEntity("EPedidotemp");
        parent::usingEntity("EPedidoDetalle");
        parent::usingEntity("ECajaDetalle");
        parent::usingEntity("ECuentaDetalle");
        parent::usingEntity("EPedidoPago");
        parent::usingData("DPedidotemp");
        parent::usingValidate("VMotivoingreso");
        parent::usingValidate("VPedidoDetalle");
        parent::usingValidate("VPedidoPago");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(PEDIDO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidotemp();
        $d->listar($o);
    }

    public function nuevo(){
        http::role(PEDIDO);
        http::put();
        $input=input();
        $o=new EPedidotemp($input);
        $d=new DPedidotemp();
        $d->nuevo($o);
    }

    public function registrar(){
        http::role(PEDIDO);
        http::put();
        $input=input();
        $o=new EPedidotemp($input);
        $d=new DPedidotemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(PEDIDO);
        http::post();
        $input=input();
        $o=new EPedidotemp($input);
        $d=new DPedidotemp();
        $d->obtener($o);
    }
    public function listarDetalle(){
        http::role(PEDIDO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidotemp();
        $d->listarDetalle($o);
    }
    public function buscarProducto(){
        http::role(PEDIDO);
        http::post();
        $o=new EFiltro(input());
        $d=new DPedidotemp();
        $d->buscarProducto($o);
    }
    public function buscarProductoNombre(){
        http::role(PEDIDO);
        http::post();
        $o=new EFiltro(input());
        $d=new DPedidotemp();
        $d->buscarProductoNombre($o);
    }
    public function buscarCliente(){
        http::role(PEDIDO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidotemp();
        $d->buscarCliente($o);
    }
    public function registrarDetalle(){
        http::role(PEDIDO);
        http::put();
        $input=input();
        $o=new EPedidoDetalle($input);
        $v=new VPedidoDetalle();
        $v->validate($o);
        $d=new DPedidotemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(PEDIDO);
        http::delete();
        $input=input();
        $o=new EPedidoDetalle($input); 
        $d=new DPedidotemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(PEDIDO);
        http::put();
        $input=input();
        $o=new EPedidotemp($input);
        $d=new DPedidotemp();
        $d->finalizar($o);
    }

    public function listarPagoCuenta(){
        http::role(PEDIDO);
        http::post();
        $d=new DPedidotemp();
        $d->listarPagoCuenta();
    }
    public function registrarPago(){
        http::role(PEDIDO);
        http::put();
        $input=input();
        $o=new EPedidoPago($input);
        $v=new VPedidoPago();
        $v->validate($o);
        $d=new DPedidotemp();
        $d->registrarPago($o);
    }
    public function eliminarPago(){
        http::role(PEDIDO);
        http::delete();
        $input=input();
        $o=new EPedidoDetalle($input); 
        $d=new DPedidotemp();
        $d->eliminarPago($o);
    }
    public function listarPago(){
        http::role(PEDIDO);
        http::post();
        $input=input();
        $o=new EPedidotemp($input);
        $d=new DPedidotemp();
        $d->listarPago($o);
    }
  
    public function listarTipopedido(){
        http::role(PEDIDO);
        http::post();
        $d=new DPedidotemp();
        $d->listarTipopedido();
    }
    public function listarUbicacion(){
        http::role(PEDIDO);
        http::post();
        $d=new DPedidotemp();
        $d->listarUbicacion();
    }
}

?>