<?php
class produccion extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DProduccion");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(PRODUCCION);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DProduccion();
        $d->listar($o);
    }
    public function obtener(){
        http::role(PRODUCCION);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DProduccion();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(PRODUCCION);
        http::post();
        $d=new DProduccion();
        $d->obtenerFiltros();
    }
}
?>