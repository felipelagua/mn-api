<?php
class producto extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DProducto");
        parent::usingEntity("EProducto");
        parent::usingEntity("EProductoDestino");
        parent::usingEntity("EFiltro");
        parent::usingValidate("VProducto");
    }

    public function registrar(){
        http::role(PRODUCTO);
        http::put();
        $input=input();
        $o=new EProducto($input);
        $v=new VProducto();
        $v->validate($o);
        $d=new DProducto();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(PRODUCTO);
        http::delete();
        $input=input();
        $o=new EProducto($input);
        $d=new DProducto();
        $d->eliminar($o);
    }
    public function listar(){
        http::role(PRODUCTO);
        http::post();
        $input=input();
        $o=new EProducto($input);
        $d=new DProducto();
        $d->listar($o);
    }
    public function obtener(){
        http::role(PRODUCTO);
        http::post();
        $input=input();
        $o=new EProducto($input);
        $d=new DProducto();
        $d->obtener($o);
    }
    public function obtenerListas(){
        http::role(PRODUCTO);
        http::post();
        $d=new DProducto();
        $d->obtenerListas();
    }
    public function buscarProducto(){
        http::role(PRODUCTO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DProducto();
        $d->buscarProducto($o);
    }
    
    public function listarDestino(){
        http::role(PRODUCTO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DProducto();
        $d->listarDestino($o);
    }
    public function registrarDestino(){
        http::role(PRODUCTO);
        http::put();
        $input=input();
        $o=new EProductoDestino($input); 
        $d=new DProducto();
        $d->registrarDestino($o);
    }
    public function eliminarDestino(){
        http::role(PRODUCTO);
        http::delete();
        $input=input();
        $o=new EProductoDestino($input); 
        $d=new DProducto();
        $d->eliminarDestino($o);
    }



    public function listarInsumo(){
        http::role(PRODUCTO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DProducto();
        $d->listarInsumo($o);
    }
    public function registrarInsumo(){
        http::role(PRODUCTO);
        http::put();
        $input=input();
        $o=new EProductoDestino($input); 
        $d=new DProducto();
        $d->registrarInsumo($o);
    }
    public function eliminarInsumo(){
        http::role(PRODUCTO);
        http::delete();
        $input=input();
        $o=new EProductoDestino($input); 
        $d=new DProducto();
        $d->eliminarInsumo($o);
    }
}

?>