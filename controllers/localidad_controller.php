<?php
class localidad extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DLocalidad");
        parent::usingEntity("ELocalidad");
        parent::usingValidate("VLocalidad");
    }

    public function registrar(){
        http::role(LOCAL);
        http::put();
        $input=input();
        $o=new ELocalidad($input);
        $v=new VLocalidad();
        $v->validate($o);
        $d=new DLocalidad();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(LOCAL);
        http::delete();
        $input=input();
        $user=new ELocalidad($input);
        $d=new DLocalidad();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(LOCAL);
        http::post();
        $input=input();
        $o=new ELocalidad($input);
        $d=new DLocalidad();
        $d->listar($o);
    }
    public function obtener(){
        http::role(LOCAL);
        http::post();
        $input=input();
        $o=new ELocalidad($input);
        $d=new DLocalidad();
        $d->obtener($o);
    }
}

?>