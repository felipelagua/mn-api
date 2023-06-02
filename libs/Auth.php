<?php
use Firebase\JWT\JWT;
require_once('./php-jwt-master/src/JWT.php');

class auth{
    public static function user(){
        $claim = auth::getClaim(); 
        $id=$claim->ide;
        return $id;
    }
    public static function name(){
        $claim = auth::getClaim(); 
        $name=$claim->name;
        return $name;
    }
    public static function grupo(){
        $claim = auth::getClaim(); 
        $id=$claim->gus;
        return $id;
    }
    public static function local(){
        $claim = auth::getClaim(); 
        $id=$claim->sub;
        return $id;
    }
    static function generateToken($a){
        $jwt_token = JWT::encode($a,JWT_SECRET_KEY);
        return $jwt_token;
    }
    static function expiration($fechaHora,$minutes){
        $minuto=60;
        $cantidad=$minutes;
        $expirationTime = $fechaHora + ($minuto*$cantidad); 
        return $expirationTime;
    }
    static function getToken(){
        $result="";
        try {
            $headers = getallheaders();
            if(isset($headers)){
                if(isset($headers['Authorization'])){
                    $bearerToken = $headers['Authorization'];
                    if(isset($bearerToken) && strlen($bearerToken)>7){
                        $bearer = substr($bearerToken, 0, 7);
                        $token = str_replace($bearer, '', $bearerToken);
                        $result= $token;
                    }
                }
            }
            if($result=="null"){$result=null;}
            return $result;
        } catch (Exception $th) {
            $result=null;
        }
        return $result;
    }
    public static function getClaim(){
        try {           
            $jwt_token=Auth::getToken();         
            if(isset($jwt_token)){                  
                $claim =JWT::decode($jwt_token,JWT_SECRET_KEY,array('HS256'));
                return $claim;
            }
            else{
                auth::gotErrorSesion("Sesión exiparada");
            }
        } 
        catch(Exception $e) {
            auth::gotErrorSesion("Sesión expirada");
        }
    }
   
    private static function gotErrorSesion($message){
        $result=new Result();
        $result->success=false; 
        $result->status=440;
        $result->error= new ResultError($message,null);
        echo json_encode($result);
        exit();
    }
 
}
?>