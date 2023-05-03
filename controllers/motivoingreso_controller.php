<?php
class Motivoingreso extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivoingreso");
        parent::usingEntity("EMotivoingreso");
        parent::usingValidate("VMotivoingreso");
    }

    public function registrar(){
        http::role(MOTIVO_INGRESO);
        http::put();
        $input=input();
        $o=new EMotivoingreso($input);
        $v=new VMotivoingreso();
        $v->validate($o);
        $d=new DMotivoingreso();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(MOTIVO_INGRESO);
        http::delete();
        $input=input();
        $user=new EMotivoingreso($input);
        $d=new DMotivoingreso();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EMotivoingreso($input);
        $d=new DMotivoingreso();
        $d->listar($o);
    }
    public function obtener(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EMotivoingreso($input);
        $d=new DMotivoingreso();
        $d->obtener($o);
    }
}

?>