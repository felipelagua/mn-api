<?php
class proveedor extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DProveedor");
        parent::usingEntity("EProveedor");
        parent::usingValidate("VProveedor");
    }

    public function registrar(){
        http::put();
        $input=input();
        
        $o=new EProveedor($input);
        $v=new VProveedor();
        $v->validate($o);
        $d=new DProveedor();

        $d->registrar($o);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $user=new EProveedor($input);
        $d=new DProveedor();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $input=input();
        $o=new EProveedor($input);
        $d=new DProveedor();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new EProveedor($input);
        $d=new DProveedor();
        $d->obtener($o);
    }
}

?>