<?php
class Caja extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DCaja");
        parent::usingEntity("ECaja");
        parent::usingEntity("ECajaDetalle");
        parent::usingValidate("VCaja");
        parent::usingValidate("VCajaDetalle");
        parent::usingValidate("VCajaTransferir");
        parent::usingEntity("ECuentaDetalle");
    }

    public function aperturar(){
        http::put();
        $input=input();
        $o=new ECaja($input);
        $v=new VCaja();
        $v->validate($o);
        $d=new DCaja();
        $d->aperturar($o);
    }
    public function registrarMovimiento(){
        http::put();
        $input=input();
        $o=new ECajaDetalle($input);
        $o->set($input);
        $v=new VCajaDetalle();
        $v->validate($o);
        $d=new DCaja();
        $d->registrarMovimiento($o);
    }
 
    public function obtener(){
        http::post();
        $input=input();
        $o=new ECaja($input);
        $d=new DCaja();
        $d->obtener($o);
    }
    public function obtenerDatosApertura(){
        $d=new DCaja();
        $d->obtenerDatosApertura();
    }
    public function obtenerDetalle(){
        http::post();
        $input=input();
        $o=new ECaja($input);
        $d=new DCaja();
        $d->obtenerDetalle($o);
    }
    public function obtenerDatosReserva(){
        $d=new DCaja();
        $d->obtenerDatosReserva();
    }
    public function reservar(){
        http::put();
        $input=input();
        $o=new ECajaDetalle($input);
        $d=new DCaja();
        $d->reservar($o);
    }
    public function obtenerPreCierre(){
        $d=new DCaja();
        $d->obtenerPreCierre();
    }
    public function finalizar(){
        http::put();
        $d=new DCaja();
        $d->finalizar();
    }
}

?>