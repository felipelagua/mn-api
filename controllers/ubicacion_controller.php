<?php
class ubicacion extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DUbicacion");
        parent::usingEntity("EUbicacion");
        parent::usingValidate("VUbicacion");
    }

    public function registrar(){
        http::role(MOTIVO_INGRESO);
        http::put();
        $input=input();
        $o=new EUbicacion($input);
        $v=new VUbicacion();
        $v->validate($o);
        $d=new DUbicacion();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(MOTIVO_INGRESO);
        http::delete();
        $input=input();
        $user=new EUbicacion($input);
        $d=new DUbicacion();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EUbicacion($input);
        $d=new DUbicacion();
        $d->listar($o);
    }
    public function obtener(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EUbicacion($input);
        $d=new DUbicacion();
        $d->obtener($o);
    }
    public function obtenerListas(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $d=new DUbicacion();
        $d->obtenerListas();
    }
}

?>