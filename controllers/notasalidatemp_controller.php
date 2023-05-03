<?php
class notasalidatemp extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivosalida");
        parent::usingEntity("EMotivosalida");
        parent::usingEntity("ENotasalidatemp");
        parent::usingEntity("ENotasalidaDetalle");
        parent::usingData("DNotasalidatemp");
        parent::usingValidate("VMotivosalida");
        parent::usingValidate("VNotasalidaDetalle");
        parent::usingEntity("EFiltro");
    }

    public function registrar(){
        http::role(NUEVA_SALIDA);
        http::put();
        $input=input();
        $o=new ENotasalidatemp($input);
        $d=new DNotasalidatemp();
        $d->registrar($o);
    }
 
    public function obtener(){
        http::role(NUEVA_SALIDA);
        http::post();
        $d=new DNotasalidatemp();
        $d->obtener();
    }
    public function listarDetalle(){
        http::role(NUEVA_SALIDA);
        http::post();
        $d=new DNotasalidatemp();
        $d->listarDetalle();
    }
    public function buscarProducto(){
        http::role(NUEVA_SALIDA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DNotasalidatemp();
        $d->buscarProducto($o);
    }
    public function registrarDetalle(){
        http::role(NUEVA_SALIDA);
        http::put();
        $input=input();
        $o=new ENotasalidaDetalle($input);
        $v=new VNotasalidaDetalle();
        $v->validate($o);
        $d=new DNotasalidatemp();
        $d->registrarDetalle($o);
    }
    public function eliminarDetalle(){
        http::role(NUEVA_SALIDA);
        http::delete();
        $input=input();
        $o=new ENotasalidaDetalle($input); 
        $d=new DNotasalidatemp();
        $d->eliminarDetalle($o);
    }
    public function finalizar(){
        http::role(NUEVA_SALIDA);
        http::put();
        $d=new DNotasalidatemp();
        $d->finalizar();
    }
}

?>