<?php
class notaingresotemp extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivoingreso");
        parent::usingEntity("EMotivoingreso");
        parent::usingEntity("ENotaingresotemp");
        parent::usingEntity("ENotaingresoDetalle");
        parent::usingData("DNotaingresotemp");
        parent::usingValidate("VMotivoingreso");
        parent::usingValidate("VNotaingresoDetalle");
        parent::usingEntity("EFiltro");
    }

    public function registrar(){
        http::role(NUEVO_INGRESO);
        http::put();
        $input=input();
        $o=new ENotaingresotemp($input);
        $d=new DNotaingresotemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(NUEVO_INGRESO);
        http::post();
        $d=new DNotaingresotemp();
        $d->obtener();
    }
    public function listarDetalle(){
        http::role(NUEVO_INGRESO);
        http::post();
        $d=new DNotaingresotemp();
        $d->listarDetalle();
    }
    public function buscarProducto(){
        http::role(NUEVO_INGRESO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotaingresotemp();
        $d->buscarProducto($o);
    }
    public function buscarProductoNombre(){
        http::role(NUEVO_INGRESO);
        http::post();
        $o=new EFiltro(input());
        $d=new DNotaingresotemp();
        $d->buscarProductoNombre($o);
    }
    public function registrarDetalle(){
        http::role(NUEVO_INGRESO);
        http::put();
        $input=input();
        $o=new ENotaingresoDetalle($input);
        $v=new VNotaingresoDetalle();
        $v->validate($o);
        $d=new DNotaingresotemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(NUEVO_INGRESO);
        http::delete();
        $input=input();
        $o=new ENotaingresoDetalle($input); 
        $d=new DNotaingresotemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(NUEVO_INGRESO);
        http::put();
        $d=new DNotaingresotemp();
        $d->finalizar();
    }
}

?>