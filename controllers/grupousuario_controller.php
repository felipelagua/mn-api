<?php
class Grupousuario extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DGrupousuario");
        parent::usingEntity("EGrupousuario");
        parent::usingEntity("EGrupousuarioacceso");
        parent::usingValidate("VGrupousuario");
    }

    public function registrar(){
        http::role(PERFIL);
        http::put();
        $input=input();        
        $o=new EGrupousuario($input);
        $v=new VGrupousuario();
        $v->validate($o);
        $d=new DGrupousuario();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(PERFIL);
        http::delete();
        $input=input();
        $user=new EGrupousuario($input);
        $d=new DGrupousuario();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(PERFIL);
        http::post();
        $input=input();
        $o=new EGrupousuario($input);
        $d=new DGrupousuario();
        $d->listar($o);
    }
    public function obtener(){
        http::role(PERFIL);
        http::post();
        $input=input();
        $o=new EGrupousuario($input);
        $d=new DGrupousuario();
        $d->obtener($o);
    }
    public function obtenerAcceso(){
        http::role(PERFIL);
        http::post();
        $input=input();
        $o=new EGrupousuario($input);
        $d=new DGrupousuario();
        $d->obtenerAcceso($o);
    }
    public function modificarAcceso(){
        http::role(PERFIL);
        http::put();
        $input=input();      
        $o=new EGrupousuarioacceso($input);
        $d=new DGrupousuario();
        $d->modificarAcceso($o);
    }
}

?>