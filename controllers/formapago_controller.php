<?php
class Formapago extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DFormapago");
        parent::usingEntity("EFormapago");
        parent::usingValidate("VFormapago");
    }

    public function registrar(){
        http::role(FORMA_PAGO);
        http::put();
        $input=input();
        $o=new EFormapago($input);
        $v=new VFormapago();
        $v->validate($o);
        $d=new DFormapago();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(FORMA_PAGO);
        http::delete();
        $input=input();
        $user=new EFormapago($input);
        $d=new DFormapago();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(FORMA_PAGO);
        http::post();
        $input=input();
        $o=new EFormapago($input);
        $d=new DFormapago();
        $d->listar($o);
    }
    public function obtener(){
        http::role(FORMA_PAGO);
        http::post();
        $input=input();
        $o=new EFormapago($input);
        $d=new DFormapago();
        $d->obtener($o);
    }
}

?>