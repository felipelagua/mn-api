<?php
class Trasladotemp extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingEntity("ETrasladotemp");
        parent::usingEntity("ETrasladoDetalle");
        parent::usingData("DTrasladotemp");
        parent::usingValidate("VTrasladoDetalle");
        parent::usingEntity("EFiltro");
    }

    public function registrar(){
        http::role(NUEVO_TRASLADO);
        http::put();
        $input=input();
        $o=new ETrasladotemp($input);
        $d=new DTrasladotemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(NUEVO_TRASLADO);
        http::post();
        $d=new DTrasladotemp();
        $d->obtener();
    }
    public function listarDetalle(){
        http::role(NUEVO_TRASLADO);
        http::post();
        $d=new DTrasladotemp();
        $d->listarDetalle();
    }
    public function buscarProducto(){
        http::role(NUEVO_TRASLADO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DTrasladotemp();
        $d->buscarProducto($o);
    }
    public function registrarDetalle(){
        http::role(NUEVO_TRASLADO);
        http::put();
        $input=input();
        $o=new ETrasladoDetalle($input);
        $v=new VTrasladoDetalle();
        $v->validate($o);
        $d=new DTrasladotemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(NUEVO_TRASLADO);
        http::delete();
        $input=input();
        $o=new ETrasladoDetalle($input); 
        $d=new DTrasladotemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(NUEVO_TRASLADO);
        http::put();
        $d=new DTrasladotemp();
        $d->finalizar();
    }
    public function asignarPedido(){
        http::role(NUEVO_TRASLADO);
        http::put();
        $input=input();
        $o=new ETrasladotemp($input);
        $d=new DTrasladotemp();
        $d->asignarPedido($o);
    }
    public function quitarPedido(){
        http::role(NUEVO_TRASLADO);
        http::put();
        $d=new DTrasladotemp();
        $d->quitarPedido();
    }
}

?>