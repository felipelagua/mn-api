<?php
function input(){
    $input = json_decode(file_get_contents('php://input'),true);
    return  $input;
}
function isGuid($value){
    $state=false;
    if(isset($value)){
        $guid = strtoupper($value);
        if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid)) {
            $state=true;
        } 
    }
    return $state;
}
    class Controller{
        public $input;
        function __construct() {
            //$this->view=new View();
            //$this->view->controller=$this;
            //$this->loadModel();
        }
        function usingData($data){
            $path="data/".$data.".php";
            if(file_exists($path)){
                include($path); 
            }
        }
        function usingEntity($entity){
            $path="entities/".$entity.".php";
            if(file_exists($path)){
                include($path); 
            }
        }

        function usingValidate($validate){
            $path="validations/".$validate.".php";
            if(file_exists($path)){
                include($path); 
            }
        }

        function loadModel(){
           $model=  get_class($this)."_model";
            $path="models/".$model.".php";
            if(file_exists($path)){
                include($path);
                $this->model=new $model();
            }
        }

        function result($state,$data,$message){
            echo json_encode(array('state'=>$state,'data'=>$data,'message' => $message));
        }

        function getValue($string){
            $string = htmlentities($string);
            $string = preg_replace('/\&(.)[^;]*;/', '\\1', $string);
            return $string;
        }

        function page($value){
            $url=  $_SERVER['REQUEST_URI'];
            $arr=explode("/",$value);
            $root=$arr[0];
            $method=$arr[1];
            $location="";
            if($url==UR || $url==UR.$root || $url==UR.$root."/"){
                $location=UR.$root."/".$method;
            }
            Session::unsetValue('ReturnUrl');
            $sesion=Session::validateSession();
            if($sesion['value']==false){
                header("location:".UR."account/login?ReturnUrl=".$value);
                Session::setValue("ReturnUrl", $value);
            }
            else{
                header("location:".$location);
            }
        }
        
        function validateApp(){
            $p = Http::post("p");
		
    		$appid='';
    		foreach ($p as $r) {
                if($r['key']=='appid'){
                  $appid=$r['val'];  
                }
            }
            $exist=false;
            if($appid!=''){
                foreach (APPS as $app) {
                    if($app==$appid){
                        $exist=true;
                    }
                }   
            }
            $exist=true;
            try {
                if (!$exist) {
                    throw new Exception('Alerta de Seguridad. La aplicación desde donde intenta acceder no es válida');
                }
                else{
                    return $exist;    
                }
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }
        
        
    }

?>
