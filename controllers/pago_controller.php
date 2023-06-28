<?php
class pago extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DPago");
        parent::usingEntity("EFiltro");
        parent::usingEntity("ECuentaDetalle");
        parent::usingEntity("EPago");
    }
    public function obtenerFiltros(){
        http::role(PAGO);
        http::post();
        $d=new DPago();
        $d->obtenerFiltros();
    }
    public function listarPeriodo(){
        http::role(PAGO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPago();
        $result = $d->listarPeriodo($o);
        ok($result);
    }
    public function obtenerDatosPago(){
        http::role(PAGO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPago();
        $d->obtenerDatosPago($o);
    }
    public function registrarPago(){
        http::role(PAGO);
        http::post();
        $input=input();
        $o=new EPago($input);
        $d=new DPago();
        $d->registrarPago($o);
    } 
}

?>