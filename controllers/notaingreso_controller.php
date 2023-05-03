<?php
class notaingreso extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DNotaingreso");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(NOTA_INGRESO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotaingreso();
        $d->listar($o);
    }
    public function obtener(){
        http::role(NOTA_INGRESO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotaingreso();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(NOTA_INGRESO);
        http::post();
        $d=new DNotaingreso();
        $d->obtenerFiltros();
    }
}
?>