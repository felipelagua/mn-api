<?php
class login extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DLogin");
        parent::usingEntity("ELogin");
        parent::usingEntity("ELocalidad");
    }
    public function autenticar(){
        http::put();
        $input=input();
        $o=new ELogin($input);
        $d=new DLogin();
        $data = $d->autenticar($o);

        $fechaHora = time();
        $expiration = Auth::expiration($fechaHora,TIMEOUT_EXPIRATIONS);
        $arrayToken=array(
            'iat' => $fechaHora,
            'exp' => $expiration,
            'ide' => $data["usuario"]["id"],
            'name' => $data['usuario']['nombre']
        );
        $jwt_token =Auth::generateToken($arrayToken);

        $r = array();
        $r['name'] = $data['usuario']['nombre'];
        $r['token'] = $jwt_token;
         $d->generateSesion($r);
    }
    public function listarLocalidad(){
        http::authorize();
        http::post();
        $d=new DLogin();
        $usuarioid=auth::user();
        $d->listarLocalidad($usuarioid);
    }
    public function asignarLocalidad(){
        http::authorize();
        http::put();
        $input=input();
        $o=new ELocalidad($input);
        $d=new DLogin();
        $claim=auth::getClaim();
        $fechaHora = time();
        $expiration = Auth::expiration($fechaHora,TIMEOUT_EXPIRATIONS);

        $grupo=$d->obtenerGrupo($claim->ide,$o->id);
        $permisos=$d->listarPermisos($grupo["grupousuarioid"]);
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
        $r['token'] = $jwt_token;
       
        $d->generateSesion($r);
    }
}

?>