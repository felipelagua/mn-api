<?php
class cuenta extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DCuenta");
        parent::usingEntity("ECuenta");
        parent::usingEntity("ECuentaDetalle");
        parent::usingEntity("ECuentaTransferir");
        parent::usingEntity("ECajaDetalle");
        parent::usingValidate("VCuenta");
        parent::usingValidate("VCuentaDetalle");
        parent::usingValidate("VCuentaTransferir");
    }

    public function registrar(){
        http::role(CUENTA);
        http::put();
        $input=input();
        $o=new ECuenta($input);
        $v=new VCuenta();
        $v->validate($o);
        $d=new DCuenta();
        $d->registrar($o);
    }
    public function registrarMovimiento(){
        http::role(CUENTA);
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
        http::role(CUENTA);
        http::delete();
        $input=input();
        $user=new ECuenta($input);
        $d=new DCuenta();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(CUENTA);
        http::post();
        $o=new ECuenta(input());
        $d=new DCuenta();
        $d->listar($o);
    }
    public function obtener(){
        http::role(CUENTA);
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DCuenta();
        $d->obtener($o);
    }
    public function obtenerListas(){
        http::role(CUENTA);
        http::post();
        $d=new DCuenta();
        $d->obtenerListas();
    }

    public function obtenerDetalle(){
        http::role(CUENTA);
        http::post();
        $input=input();
        $o=new ECuenta($input);
        $d=new DCuenta();
        $d->obtenerDetalle($o);
    }
    public function obtenerListasMov(){
        http::role(CUENTA);
        http::post();
        $d=new DCuenta();
        $d->obtenerListasMov();
    }
    public function transferir(){
        http::role(TRANSFERIR);
        http::put();
        $o=new ECuentaTransferir();
        $v=new VCuentaTransferir();
        $v->validate($o);
        $d=new DCuenta();
        $d->transferir($o);
    }

    public function obtenerListasTransferir(){
        http::role(TRANSFERIR);
        http::post();
        $d=new DCuenta();
        $d->obtenerListasTransferir();
    }
}

?>