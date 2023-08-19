<?php
using("data/DCaja"); 
using("entities/ECaja");
using("entities/EFiltro");
using("entities/ECajaDetalle");
using("entities/ECuentaDetalle");
using("validations/VCaja");
using("validations/VCajaDetalle");
using("validations/VCajaTransferir");
class Caja extends Controller{
    private $d;
    private $v;
    public function __construct(){
        parent::__construct();
        $this->d= new DCaja();
        $this->v= new  VCaja();
        
    }
    public function listar(){
        http::role(CAJA_REPORTE);
        http::post();
        $o=new EFiltro(input());
        $this->d->listar($o);
    }
    public function obtenerFiltros(){
        http::role(CAJA_REPORTE);
        http::post();
        $this->d->obtenerFiltros();
    }
    public function aperturar(){
        http::role(CAJA);
        http::put();
        $o=new ECaja(input()); 
        $this->v->validate($o);
        $this->d->aperturar($o);
    }
    public function registrarMovimiento(){
        http::role(CAJA);
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
        http::role(CAJA);
        http::post();
        $input=input();
        $o=new ECaja($input);
        $d=new DCaja();
        $d->obtener($o);
    }
    public function obtenerDatosApertura(){
        http::role(CAJA);
        $d=new DCaja();
        $d->obtenerDatosApertura();
    }
    public function obtenerDetalle(){
        http::role(CAJA);
        http::post();
        $input=input();
        $o=new ECaja($input);
        $d=new DCaja();
        $d->obtenerDetalle($o);
    }
    public function obtenerDetalleCierre(){
        http::role(CAJA_REPORTE);
        http::post();
        $o=new ECaja(input());
        $this->d->obtenerDetalleCierre($o);
    }
    public function obtenerDatosReserva(){
        http::role(CAJA);
        $d=new DCaja();
        $d->obtenerDatosReserva();
    }
    public function reservar(){
        http::role(CAJA);
        http::put();
        $input=input();
        $o=new ECajaDetalle($input);
        $d=new DCaja();
        $d->reservar($o);
    }
    public function obtenerPreCierre(){
        http::role(CAJA);
        $d=new DCaja();
        $data=$d->obtenerPreCierre();
        $data["filename"]="MNCRJ";
        http::gotoSuccess($data);
         
    }
    public function finalizar(){
        http::role(CAJA);
        http::put();
        $d=new DCaja();
        $d->finalizar();
    }
}

?>