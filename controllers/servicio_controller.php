<?php
class servicio extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DServicio");
        parent::usingEntity("EServicio");
        parent::usingEntity("EServicioEnt");
        parent::usingEntity("EServicioCompra");
        parent::usingEntity("EFiltro");
        parent::usingEntity("ECuentaDetalle");      
    }

    public function registrar(){
        http::role(SERVICIO);
        http::put();
        $input=input();
        $o=new EServicio($input);
        $d=new DServicio();
        $d->registrar($o);
    }
    public function eliminar(){
        http::role(SERVICIO);
        http::delete();
        $input=input();
        $user=new EServicio($input);
        $d=new DServicio();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(SERVICIO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DServicio();
        $d->listar($o);
    }
    public function obtener(){
        http::role(SERVICIO);
        http::post();
        $input=input();
        $o=new EServicio($input);
        $d=new DServicio();
        $d->obtener($o);
    }
    public function obtenerLista(){
        http::role(SERVICIO);
        http::post();
        $d=new DServicio();
        $d->obtenerLista();
    }
    public function buscarProveedor(){
        http::role(SERVICIO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DServicio();
        $d->buscarProveedor($o);
    }
    public function buscarProducto(){
        http::role(SERVICIO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DServicio();
        $d->buscarProducto($o);
    }
    public function obtenerFiltros(){
        http::role(SERVICIO);
        http::post();
        $d=new DServicio();
        $d->obtenerFiltros();
    }
    public function listarPeriodo(){
        http::role(PAGOSERVICIO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DServicio();
        $result = $d->listarPeriodo($o);
        ok($result);
    }
    public function obtenerDatosPago(){
        http::role(PAGOSERVICIO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DServicio();
        $d->obtenerDatosPago($o);
    }
    public function registrarPago(){
        http::role(PAGOSERVICIO);
        http::post();
        $input=input();
        $o=new EServicioCompra($input);
        $d=new DServicio();
        $d->registrarPago($o);
    }
}

?>