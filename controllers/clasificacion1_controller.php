<?php
class clasificacion1 extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DClasificacion1");
        parent::usingEntity("EClasificacion1");
        parent::usingValidate("VClasificacion1");
    }

    public function registrar(){
        http::role(MOTIVO_INGRESO);
        http::put();
        $input=input();
        $o=new EClasificacion1($input);
        $v=new VClasificacion1();
        $v->validate($o);
        $d=new DClasificacion1();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(MOTIVO_INGRESO);
        http::delete();
        $input=input();
        $user=new EClasificacion1($input);
        $d=new DClasificacion1();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EClasificacion1($input);
        $d=new DClasificacion1();
        $d->listar($o);
    }
    public function obtener(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $input=input();
        $o=new EClasificacion1($input);
        $d=new DClasificacion1();
        $d->obtener($o);
    }
}

?>