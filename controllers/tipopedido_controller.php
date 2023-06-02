<?php
class tipopedido extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DTipopedido");
        parent::usingEntity("ETipopedido");
        parent::usingValidate("VTipopedido");
    }

    public function registrar(){
        http::role(TIPO_PEDIDO);
        http::put();
        $input=input();
        $o=new ETipopedido($input);
        $v=new VTipopedido();
        $v->validate($o);
        $d=new DTipopedido();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(TIPO_PEDIDO);
        http::delete();
        $input=input();
        $user=new ETipopedido($input);
        $d=new DTipopedido();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(TIPO_PEDIDO);
        http::post();
        $input=input();
        $o=new ETipopedido($input);
        $d=new DTipopedido();
        $d->listar($o);
    }
    public function obtener(){
        http::role(TIPO_PEDIDO);
        http::post();
        $input=input();
        $o=new ETipopedido($input);
        $d=new DTipopedido();
        $d->obtener($o);
    }
}

?>