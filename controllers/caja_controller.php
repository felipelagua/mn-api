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
        http::role(CAJA_APERTURA);
        http::put();
        $input=input();
        $o=new ECaja($input);
        $v=new VCaja();
        $v->validate($o);
        $d=new DCaja();
        $d->aperturar($o);
    }
    public function registrarMovimiento(){
        http::role(CAJA_APERTURA);
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
        http::role(CAJA_APERTURA);
        http::post();
        $input=input();
        $o=new ECaja($input);
        $d=new DCaja();
        $d->obtener($o);
    }
    public function obtenerDatosApertura(){
        http::role(CAJA_APERTURA);
        $d=new DCaja();
        $d->obtenerDatosApertura();
    }
    public function obtenerDetalle(){
        http::role(CAJA_DETALLE);
        http::post();
        $input=input();
        $o=new ECaja($input);
        $d=new DCaja();
        $d->obtenerDetalle($o);
    }
    public function obtenerDatosReserva(){
        http::role(CAJA_RESERVA);
        $d=new DCaja();
        $d->obtenerDatosReserva();
    }
    public function reservar(){
        http::role(CAJA_RESERVA);
        http::put();
        $input=input();
        $o=new ECajaDetalle($input);
        $d=new DCaja();
        $d->reservar($o);
    }
    public function obtenerPreCierre(){
        http::role(CAJA_CIERRE);
        $d=new DCaja();
        $d->obtenerPreCierre();
    }
    public function finalizar(){
        http::role(CAJA_CIERRE);
        http::put();
        $d=new DCaja();
        $d->finalizar();
    }
}

?>