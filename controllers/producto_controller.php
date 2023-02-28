<?php
class producto extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DProducto");
        parent::usingEntity("EProducto");
        parent::usingValidate("VProducto");
    }

    public function registrar(){
        http::put();
        $input=input();
        $user=new EProducto($input);
        $v=new VProducto();
        $v->validate($user);
        $d=new DProducto();

        $d->registrar($user);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $o=new EProducto($input);
        $d=new DProducto();
        $d->eliminar($o);
    }
    public function listar(){
        http::post();
        $d=new DProducto();
        $d->listar();
    }
}

?>