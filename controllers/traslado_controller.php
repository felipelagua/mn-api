<?php
class Traslado extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DTraslado");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(TRASLADO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DTraslado();
        $d->listar($o);
    }
    public function obtener(){
        http::role(TRASLADO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DTraslado();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(TRASLADO);
        http::post();
        $d=new DTraslado();
        $d->obtenerFiltros();
    }
}

?>