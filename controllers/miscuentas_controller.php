<?php
class miscuentas extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMicuenta");
        parent::usingEntity("ECuenta");
        parent::usingEntity("ECuentaDetalle");
        parent::usingEntity("ECuentaTransferir");
        parent::usingValidate("VCuenta");
        parent::usingValidate("VCuentaDetalle");
        parent::usingValidate("VCuentaTransferir");
    }

    public function registrar(){
        http::role(MIS_CUENTAS);
        http::put();
        $input=input();
        $o=new ECuenta($input);
        $v=new VCuenta();
        $v->validate($o);
        $d=new DCuenta();
        $d->registrar($o);
    }
    public function registrarMovimiento(){
        http::role(MIS_CUENTAS);
        http::put();
        $input=input();
        $o=new ECuentaDetalle($input);
        $o->set($input);
        $v=new VCuentaDetalle();
        $v->validate($o);
        $d=new DCuenta();
        $d->registrarMovimiento($o);
    }
    public function listar(){
        http::role(MIS_CUENTAS);
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DMicuenta();
        $d->listar($o);
    }
    public function obtener(){
        http::role(MIS_CUENTAS);
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DMicuenta();
        $d->obtener($o);
    }
 
    public function obtenerDetalle(){
        http::role(MIS_CUENTAS);
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DMicuenta();
        $d->obtenerDetalle($o);
    }
   
    public function devolver(){
        http::role(MIS_CUENTAS);
        http::put();
        $input=input();
        $o=new ECuenta($input);
        $d=new DMicuenta();
        $d->devolver($o);
    }
}

?>