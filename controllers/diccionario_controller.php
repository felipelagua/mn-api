<?php
using("data/DDiccionario");
using("entities/EDiccionario");
using("validations/VDiccionario");

class diccionario extends Controller{
    private $d;
    private $v;
    public function __construct(){
        parent::__construct();
        $this->d= new DDiccionario();
        $this->v= new  VDiccionario();
    }

    public function registrar(){
        http::role(MOTIVO_INGRESO);
        http::put();
        $o=new EDiccionario(input());
        $this->v->validate($o);
        $this->d->registrar($o);
    }
    public function eliminar(){
        http::role(MOTIVO_INGRESO);
        http::delete();
        $user=new EDiccionario(input());
        $this->d->eliminar($user);
    }
    public function listar(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $o=new EDiccionario(input());
        $this->d->listar($o);
    }
    public function obtener(){
        http::role(MOTIVO_INGRESO);
        http::post();
        $o=new EDiccionario(input()); 
        $this->d->obtener($o);
    }
}

?>