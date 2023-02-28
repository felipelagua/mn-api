<?php
class motivoma extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DMotivoma");
        parent::usingEntity("EMotivoma");
        parent::usingValidate("VMotivoma");
    }

    public function registrar(){
        http::put();
        $input=input();
        
        $o=new EMotivoma($input);
        $v=new VMotivoma();
        $v->validate($o);
        $d=new DMotivoma();

        $d->registrar($o);
    }
    public function eliminar(){
        http::delete();
        $input=input();
        $user=new EMotivoma($input);
        $d=new DMotivoma();
        $d->eliminar($user);
    }
    public function listar(){
        http::post();
        $d=new DMotivoma();
        $d->listar();
    }
}

?>