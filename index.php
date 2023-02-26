<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');
header( 'Content-Type: application/json' );

include("config.php");
 

$url=(isset($_GET["url"])?$_GET["url"]:"home/index");
$public_url=$url;
if($url=="index.php"){$url="home/index";}
$url=explode("/",$url);
define("LIBS","libs/");
define("ERROR_METHOD","El method METHOD_NAME not found");
define("ERROR_CONTROLLER","The controller CONTROLLER_NAME not found");

if(isset($url[0])){$controller=$url[0];}
if(isset($url[1])){$method=$url[1];}
else{$method="index";}

if(isset($url[2])){$params=$url[2];}

$libs_dir=opendir(LIBS);
while($file=  readdir($libs_dir)){
  if (!is_dir($file) && (strpos($file,'.php') != false) ){
      include(LIBS.$file);
  }
}


$path="controllers/".$controller."_controller.php";
if(file_exists($path)){
    include($path);
    $controller= new $controller();
    if(isset($method)){
        if(method_exists($controller,$method)){
            if(isset($params)){
                $controller->{$method}($params);
            }
            else{
                $controller->{$method}();
            }
        }
        else{
            gotoError(str_replace("METHOD_NAME", $method,ERROR_METHOD));
        }
    }
    else{

        gotoError(gotoError("METHOD_NAME", $method,ERROR_METHOD));
    }
}
else{
    gotoError(gotoError("CONTROLLER_NAME", $controller,ERROR_CONTROLLER));
}

function gotoError($message){
    $result=new Result();          
    $result->success=false;
    $result->error= new ResultError($message,null);
    echo json_encode($result);
    exit(); 
}
?>
