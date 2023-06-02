<?php
class stock extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DStock");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(STOCK);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DStock();
        $result = $d->listar($o);
        ok($result);
    }
    public function obtener(){
        http::role(STOCK);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DStock();
        $d->obtener($o);
    }
    public function pdf(){
        http::role(STOCK);
        http::post();
        $input=input();
        $localidadid=auth::local();
        $o=new EFiltro($input);
        $d=new DStock();

        $data = $d->listar($o);
        $loc = $d->obtenerLocalidad($localidadid);
        $pdf = new PDF();
        $pdf->setTitle("STOCK DE ALMACEN");
        $pdf->setLocal($loc["nombre"]);

        $head = array('PRODUCTO', 'STOCK','STOCK FISICO');
        $align=array('L', 'R','R');
        $field = array('nombre', 'cantidad','');
        $width= array(130,30,30);
        $pdf->setTable($data,$head,$field,$align,$width);
        $pdf->download("stock");
    }
 
}

?>