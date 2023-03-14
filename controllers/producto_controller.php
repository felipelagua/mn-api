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
        $o=new EProducto($input);
        $v=new VProducto();
        $v->validate($o);
        $d=new DProducto();

        $d->registrar($o);
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
        $input=input();
        $o=new EProducto($input);
        $d=new DProducto();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
     
        $o=new EProducto($input);
        $d=new DProducto();
        $d->obtener($o);
    }
}

?>