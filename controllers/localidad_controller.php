<?php
class localidad extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DLocalidad");
        parent::usingEntity("ELocalidad");
        parent::usingValidate("VLocalidad");
    }

    public function registrar(){
        http::put();
        $input=input();
        $user=new ELocalidad($input);
        $v=new VLocalidad();
        $v->validate($user);
        $d=new DLocalidad();

        $d->registrar($user);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $user=new ELocalidad($input);
        $d=new DLocalidad();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $d=new DLocalidad();
        $d->listar();
    }
}

?>