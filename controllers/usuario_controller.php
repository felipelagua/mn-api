<?php
class usuario extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DUsuario");
        parent::usingEntity("EUsuario");
        parent::usingValidate("VUsuario");
    }

    public function registrar(){
        http::put();
        $input=input();
        $o=new EUsuario($input);
        $v=new VUsuario();
        $v->validate($o);
        $d=new DUsuario();

        $d->registrar($o);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $user=new EUsuario($input);
        $d=new DUsuario();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $input=input();
        $o=new EUsuario($input);
        $d=new DUsuario();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new EUsuario($input);
        $d=new DUsuario();
        $d->obtener($o);
    }
}

?>