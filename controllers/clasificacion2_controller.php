<?php
class clasificacion2 extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DClasificacion2");
        parent::usingEntity("EClasificacion2");
        parent::usingValidate("VClasificacion2");
    }

    public function registrar(){
        http::role(MOTIVO_INGRESO);
        http::put();
        $input=input();
        $o=new EClasificacion2($input);
        $v=new VClasificacion2();
        $v->validate($o);
        $d=new DClasificacion2();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(MOTIVO_INGRESO);
        http::delete();
        $input=input();
        $user=new EClasificacion2($input);
        $d=new DClasificacion2();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EClasificacion2($input);
        $d=new DClasificacion2();
        $d->listar($o);
    }
    public function obtener(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EClasificacion2($input);
        $d=new DClasificacion2();
        $d->obtener($o);
    }
}

?>