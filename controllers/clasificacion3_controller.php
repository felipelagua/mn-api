<?php
class clasificacion3 extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DClasificacion3");
        parent::usingEntity("EClasificacion3");
        parent::usingValidate("VClasificacion3");
    }

    public function registrar(){
        http::role(MOTIVO_INGRESO);
        http::put();
        $input=input();
        $o=new EClasificacion3($input);
        $v=new VClasificacion3();
        $v->validate($o);
        $d=new DClasificacion3();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(MOTIVO_INGRESO);
        http::delete();
        $input=input();
        $user=new EClasificacion3($input);
        $d=new DClasificacion3();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EClasificacion3($input);
        $d=new DClasificacion3();
        $d->listar($o);
    }
    public function obtener(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EClasificacion3($input);
        $d=new DClasificacion3();
        $d->obtener($o);
    }
}

?>