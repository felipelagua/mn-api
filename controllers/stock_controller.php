<?php
using("data/DStock"); 
using("entities/EFiltro");
using("entities/ELocalidadProducto");

class stock extends Controller{
    private $d;
    public function __construct(){
        parent::__construct();
        $this->d= new DStock();
    }
    public function listar(){
        http::role(STOCK);http::post();
        $o=new EFiltro(input());
        $data = $this->d->listar($o);
        ok($data);
    }
    public function listarStock(){
        http::role(CSTOCK);http::post();
        $o=new EFiltro(input());
        $data = $this->d->listarStock($o);
        ok($data);
    }
    public function obtenerFiltros(){
        http::role(CSTOCK);http::post();
        $this->d->obtenerFiltros();
    }
    public function obtener(){
        http::role(STOCK);http::post();
        $o=new EFiltro(input());
        $this->d->obtener($o);
    }
    public function obtenerDetalleStock(){
        http::role(CSTOCK);http::post();
        $o=new EFiltro(input());
        $this->d->obtenerDetalleStock($o);
    }
    public function obtenerStock(){
        http::role(CSTOCK);http::post();
        $o=new EFiltro(input());
        $this->d->obtenerStock($o);
    }
    public function registrarStock(){
        http::role(CSTOCK);http::put();
        $o=new ELocalidadProducto(input());
        $this->d->registrarStock($o);
    }
}

?>