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
    

}
?>