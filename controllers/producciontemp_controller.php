<?php
class Producciontemp extends Controller{
    public function __construct(){
        parent::__construct();
 
        parent::usingEntity("EProducciontemp");
        parent::usingEntity("EProduccionDetalle");
        parent::usingData("DProducciontemp");
        parent::usingValidate("VProduccionDetalle");
        parent::usingEntity("EFiltro");
    }

    public function registrar(){
        http::role(NUEVO_INGRESO);
        http::put();
        $input=input();
        $o=new EProducciontemp($input);
        $d=new DProducciontemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(NUEVO_INGRESO);
        http::post();
        $d=new DProducciontemp();
        $d->obtener();
    }
    public function listarDetalle(){
        http::role(NUEVO_INGRESO);
        http::post();
        $d=new DProducciontemp();
        $d->listarDetalle();
    }
    public function buscarProducto(){
        http::role(NUEVO_INGRESO);
        http::post();
        $o=new EFiltro(input());
        $d=new DProducciontemp();
        $d->buscarProducto($o);
    }
    public function buscarProductoNombre(){
        http::role(NUEVO_INGRESO);
        http::post();
        $o=new EFiltro(input());
        $d=new DProducciontemp();
        $d->buscarProductoNombre($o);
    }
    public function registrarDetalle(){
        http::role(NUEVO_INGRESO);
        http::put();
        $input=input();
        $o=new EProduccionDetalle($input);
        $v=new VProduccionDetalle();
        $v->validate($o);
        $d=new DProducciontemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(NUEVO_INGRESO);
        http::delete();
        $input=input();
        $o=new EProduccionDetalle($input); 
        $d=new DProducciontemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(NUEVO_INGRESO);
        http::put();
        $d=new DProducciontemp();
        $d->finalizar();
    }
}

?>