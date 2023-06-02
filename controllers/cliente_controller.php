<?php
class cliente extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DCliente");
        parent::usingEntity("ECliente");
        parent::usingValidate("VCliente");
    }

    public function registrar(){
        http::role(PROVEEDOR);
        http::put();
        $input=input();      
        $o=new ECliente($input);
        $v=new VCliente();
        $v->validate($o);
        $d=new DCliente();
        $d->registrar($o);
    }
    public function eliminar(){
        http::role(PROVEEDOR);
        http::delete();
        $input=input();
        $user=new ECliente($input);
        $d=new DCliente();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(PROVEEDOR);
        http::post();
        $input=input();
        $o=new ECliente($input);
        $d=new DCliente();
        $d->listar($o);
    }
    public function obtener(){
        http::role(PROVEEDOR);
        http::post();
        $input=input();
        $o=new ECliente($input);
        $d=new DCliente();
        $d->obtener($o);
    }
}

?>