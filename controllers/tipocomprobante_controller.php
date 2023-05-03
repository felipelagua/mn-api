<?php
class tipocomprobante extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DTipocomprobante");
        parent::usingEntity("ETipocomprobante");
        parent::usingValidate("VTipocomprobante");
    }

    public function registrar(){
        http::role(TIPO_COMPROBANTE);
        http::put();
        $input=input();        
        $o=new ETipocomprobante($input);
        $v=new VTipocomprobante();
        $v->validate($o);
        $d=new DTipocomprobante();
        $d->registrar($o);
    }
    public function eliminar(){
        http::role(TIPO_COMPROBANTE);
        http::delete();
        $input=input();
        $user=new ETipocomprobante($input);
        $d=new DTipocomprobante();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(TIPO_COMPROBANTE);
        http::post();
        $input=input();
        $o=new ETipocomprobante($input);
        $d=new DTipocomprobante();
        $d->listar($o);
    }
    public function obtener(){
        http::role(TIPO_COMPROBANTE);
        http::post();
        $input=input();
        $o=new ETipocomprobante($input);
        $d=new DTipocomprobante();
        $d->obtener($o);
    }
}

?>