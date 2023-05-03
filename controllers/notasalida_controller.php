<?php
class notasalida extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DNotasalida");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(NOTA_SALIDA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotasalida();
        $d->listar($o);
    }
    public function obtener(){
        http::role(NOTA_SALIDA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotasalida();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(NOTA_SALIDA);
        http::post();
        $d=new DNotasalida();
        $d->obtenerFiltros();
    }
}

?>