<?php
class usuario extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DUsuario");
        parent::usingEntity("EUsuario");
        parent::usingEntity("EUsuarioacceso");
        parent::usingValidate("VUsuario");
    }

    public function registrar(){
        http::role(USUARIO);
        http::put();
        $input=input();
        $o=new EUsuario($input);
        $v=new VUsuario();
        $v->validate($o);
        $d=new DUsuario();

        $d->registrar($o);
    }
    public function eliminar(){
        http::role(USUARIO);
        http::delete();
        $input=input();
        $user=new EUsuario($input);
        $d=new DUsuario();
        $d->eliminar($user);
    }
    public function listar(){
        http::role(USUARIO);
        http::post();
        $input=input();
        $o=new EUsuario($input);
        $d=new DUsuario();
        $d->listar($o);
    }
    public function obtener(){
        http::role(USUARIO);
        http::post();
        $input=input();
        $o=new EUsuario($input);
        $d=new DUsuario();
        $d->obtener($o);
    }
    public function registrarAcceso(){
        http::role(USUARIO);
        http::put();
        $input=input();
        $o=new EUsuarioacceso($input);
        $d=new DUsuario();
        $d->registrarAcceso($o);
    }
    public function eliminarAcceso(){
        http::role(USUARIO);
        http::delete();
        $input=input();
        $o=new EUsuarioacceso($input);
        $d=new DUsuario();
        $d->eliminarAcceso($o);
    }
    public function listarAcceso(){
        http::role(USUARIO);
        http::post();
        $input=input();
        $o=new EUsuario($input);
        $d=new DUsuario();
        $d->listarAcceso($o);
    }

}

?>