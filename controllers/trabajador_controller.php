<?php
class trabajador extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DTrabajador");
        parent::usingEntity("ETrabajador");
        parent::usingValidate("VTrabajador");
    }

    public function registrar(){
        http::role(TRABAJADOR);
        http::put();
        $input=input();
        $o=new ETrabajador($input);
        $v=new VTrabajador();
        $v->validate($o);
        $d=new DTrabajador();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(TRABAJADOR);
        http::delete();
        $input=input();
        $user=new ETrabajador($input);
        $d=new DTrabajador();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(TRABAJADOR);
        http::post();
        $input=input();
        $o=new ETrabajador($input);
        $d=new DTrabajador();
        $d->listar($o);
    }
    public function obtener(){
        http::role(TRABAJADOR);
        http::post();
        $input=input();
        $o=new ETrabajador($input);
        $d=new DTrabajador();
        $d->obtener($o);
    }
    public function listarLocalidad(){
        http::role(TRABAJADOR);
        http::post();
        $d=new DTrabajador();
        $d->listarLocalidad();
    }
}

?>