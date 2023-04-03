<?php
class stock extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DStock");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DStock();
        $d->listar($o);
    }
    public function obtener(){
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DStock();
        $d->obtener($o);
    }
 
}

?>