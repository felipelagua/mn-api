<?php
class dashboard extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DDashboard"); 
    }
 
    public function listarAcceso(){
        http::authorize();
        http::post();
        $d=new DDashboard();
        $d->listarAcceso();
    }
 
}

?>