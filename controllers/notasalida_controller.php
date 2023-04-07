<?php
class notasalida extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DNotasalida");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotasalida();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotasalida();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::post();
        $d=new DNotasalida();
        $d->obtenerFiltros();
    }
}

?>