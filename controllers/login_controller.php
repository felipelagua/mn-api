<?php
using("data/DLogin");
using("data/DLocalidad");
using("validations/VLogin");
using("entities/ELogin");
using("entities/ELocalidad");

class login extends Controller{
    private $v;
    private $d;
    private $cps="542b4b0d34c411ee97f8b4a9fce2d243";
    private $ncps="15e4b04434c911ee97f8b4a9fce2d243";
    public function __construct(){
        parent::__construct();
        $this->v = new VLogin();
        $this->d = new DLogin();
    }
    public function autenticar(){
        http::put();
        $o=new ELogin(input());
        $this->v->validate($o);
        $data = $this->d->autenticar($o);
 
        $fechaHora = time();
        $expiration = Auth::expiration($fechaHora,TIMEOUT_EXPIRATIONS);
        $cps=$o->clave=="123"?$this->cps:$this->ncps;

        $arrayToken=array(
            'iat' => $fechaHora,
            'exp' => $expiration,
            'ide' => $data["usuario"]["id"],
            'name' => $data['usuario']['nombre'],
            'cps' => $cps,
        );
        $jwt_token =Auth::generateToken($arrayToken);

        $r = array();
        $r['name'] = $data['usuario']['nombre'];
        $r['token'] = $jwt_token;
        $r['cps'] = $cps;
       ok($r);
 
    }
    public function listarLocalidad(){
        http::authorize();http::post();
        $claim=auth::getClaim();
        if($claim->cps!=$this->ncps){
            http::gotError("No está autorizado, inicia sesión",false);
        }
        $data=$this->d->listarLocalidad();
        ok($data);
    }
    public function changepass(){
        http::authorize();http::put();
        $o=new ELogin(input());
         $this->d->cambiarClave($o);
    }
    public function asignarLocalidad(){
        http::authorize();http::put();
        $claim=auth::getClaim();
        if($claim->cps!=$this->ncps){
            http::gotError("No está autorizado, inicia sesión",false);
        }
        $o=new ELocalidad(input());
        $d=new DLogin();

        $fechaHora = time();
        $expiration = Auth::expiration($fechaHora,TIMEOUT_EXPIRATIONS);

        $grupo=$d->obtenerGrupo($claim->ide,$o->id);
        $permisos=$d->listarPermisos($grupo["grupousuarioid"]);
        $dloc=new DLocalidad();
        $loc = $dloc->obtenerLocalidad($o->id);
        $arrayToken=array(
            'iat' => $fechaHora,
            'exp' => $expiration,
            'ide' => $claim->ide,
            'name' => $claim->name,
            'sub' => $o->id,
            'gus' => $grupo["grupousuarioid"],
            'prs'=>$permisos
        );
        $jwt_token =Auth::generateToken($arrayToken);

        $r = array();
        $r['name'] = $claim->name;
        $r['ptr'] = $loc["venta"];
        $r['token'] = $jwt_token;
        $r['diccionario'] = $d->listarDiccionario();
        ok($r);
    }
}

?>