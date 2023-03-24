<?php
class Motivosalida extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivosalida");
        parent::usingEntity("EMotivosalida");
        parent::usingValidate("VMotivosalida");
    }

    public function registrar(){
        http::put();
        $input=input();
        
        $o=new EMotivosalida($input);
        $v=new VMotivosalida();
        $v->validate($o);
        $d=new DMotivosalida();

        $d->registrar($o);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $user=new EMotivosalida($input);
        $d=new DMotivosalida();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $input=input();
        $o=new EMotivosalida($input);
        $d=new DMotivosalida();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new EMotivosalida($input);
        $d=new DMotivosalida();
        $d->obtener($o);
    }
}

?>