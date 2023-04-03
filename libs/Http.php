<?php
class http{
    public static function get(){$method="GET";http::validateHttpMethod($method);}
    public static function post(){$method="POST";http::validateHttpMethod($method);}
    public static function put(){$method="PUT";http::validateHttpMethod($method);}
    public static function delete(){$method="DELETE";http::validateHttpMethod($method);}

    static function validateHttpMethod($method){ 
        $request_method = $_SERVER['REQUEST_METHOD'];
        if($request_method!=$method){
            $result=new Result();
            $result->success=false;
            $message="El método $request_method no está permitido";
            $result->error= new ResultError($message,null);
            echo json_encode($result);
            exit();
        }
    }
    static function authorize(){
        $token=auth::getToken();
        if($token==null){
            http::gotError("401 - No Autorizado",401);
        }
    }
    public static function role($param){
        http::authorize();
        $claim = Auth::getClaim(); 
        $arrPermisions = $claim->prs;$state=false;
        if(isset($arrPermisions) && count($arrPermisions)>0){
            foreach($arrPermisions as $item){ if($item ==$param){ $state=true; } }
        }
        if(!$state){
            http::gotError("403 - No Autorizado",403);
        }
    }
    static function gotError($message,$state){
        $result=new Result();
        $result->success=false;
        $result->status=$state;
        $result->error= new ResultError($message,null);
        echo json_encode($result);
        exit();
    }
}
?>