<?php
class cuenta extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DCuenta");
        parent::usingEntity("ECuenta");
        parent::usingEntity("ECuentaDto");
        parent::usingEntity("ECuentaDetalle");
        parent::usingEntity("ECuentaTransferir");
        parent::usingValidate("VCuenta");
        parent::usingValidate("VCuentaDetalle");
        parent::usingValidate("VCuentaTransferir");
    }

    public function registrar(){
        http::put();
        $input=input();
        $o=new ECuenta($input);
        $v=new VCuenta();
        $v->validate($o);
        $d=new DCuenta();
        $d->registrar($o);
    }
    public function registrarMovimiento(){
        http::put();
        $input=input();
        $o=new ECuentaDetalle($input);
        $o->set($input);
        $v=new VCuentaDetalle();
        $v->validate($o);
        $d=new DCuenta();
        $d->registrarMovimiento($o);
    }

    public function eliminar(){
        http::delete();
        $input=input();
        $user=new ECuenta($input);
        $d=new DCuenta();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $d=new DCuenta();
        $d->listar();
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DCuenta();
        $d->obtener($o);
    }
    public function transferir(){
        http::put();
        $o=new ECuentaTransferir();
        $v=new VCuentaTransferir();
        $v->validate($o);
        $d=new DCuenta();
        $d->transferir($o);
    }
    public function obtenerListas(){
        http::post();
        $d=new DCuenta();
        $d->obtenerListas();
    }
    public function obtenerListasTransferir(){
        http::post();
        $d=new DCuenta();
        $d->obtenerListasTransferir();
    }
    public function obtenerDetalle(){
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DCuenta();
        $d->obtenerDetalle($o);
    }
    public function obtenerListasMov(){
        http::post();
        $d=new DCuenta();
        $d->obtenerListasMov();
    }
}

?>