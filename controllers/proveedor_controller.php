<?php
class proveedor extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DProveedor");
        parent::usingEntity("EProveedor");
        parent::usingValidate("VProveedor");
    }

    public function registrar(){
        http::role(PROVEEDOR);
        http::put();
        $input=input();      
        $o=new EProveedor($input);
        $v=new VProveedor();
        $v->validate($o);
        $d=new DProveedor();
        $d->registrar($o);
    }
    public function eliminar(){
        http::role(PROVEEDOR);
        http::delete();
        $input=input();
        $user=new EProveedor($input);
        $d=new DProveedor();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(PROVEEDOR);
        http::post();
        $input=input();
        $o=new EProveedor($input);
        $d=new DProveedor();
        $d->listar($o);
    }
    public function obtener(){
        http::role(PROVEEDOR);
        http::post();
        $input=input();
        $o=new EProveedor($input);
        $d=new DProveedor();
        $d->obtener($o);
    }
}

?>