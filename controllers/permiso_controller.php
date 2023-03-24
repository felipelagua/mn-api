<?php
class permiso extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DPermiso");
        parent::usingEntity("EPermiso");
        parent::usingValidate("VPermiso");
    }

    public function registrar(){
        http::put();
        $input=input();
        $o=new EPermiso($input);
        $v=new VPermiso();
        $v->validate($o);
        $d=new DPermiso();

        $d->registrar($o);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $user=new EPermiso($input);
        $d=new DPermiso();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $input=input();
        $o=new EPermiso($input);
        $d=new DPermiso();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new EPermiso($input);
        $d=new DPermiso();
        $d->obtener($o);
    }
}

?>