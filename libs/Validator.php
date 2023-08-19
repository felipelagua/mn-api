<?php
    class Validator{
        public $details=[];

        function required($value,$label){
            if(!isset($value) || strlen($value)==0){
                array_push($this->details,$label);
            }
        }
        function typeOperation($value){
            if(!isset($value) || strlen($value)==0){
                array_push($this->details,"El tipo de operación es obligatorio");
            }
            else{
                if($value!="ING" && $value!="SAL"){
                    array_push($this->details,"El tipo de operación no es válido");
                }
            }
        }
        function numberValue($value,$message){
            if(isset($value)){
                if (!is_numeric($value) || $value<0) {
                    array_push($this->details,$message);
                }
            }
        }
        function mayorque($value,$min,$message){
            if(isset($value)){
                if (!is_numeric($value) || $value<$min) {
                    array_push($this->details,$message);
                }
            }
            else{
                array_push($this->details,$message);
            }
        }
        function minLength($value,$minLength,$label){
            if($value==null ){
                array_push($this->details,"El campo $label es obligatorio");
            }
            else{
                
                if(strlen($value)>0 && strlen($value)<$minLength){
                    array_push($this->details,"El campo $label debe tener por lo menos $minLength caracateres");
                }
            }  
        }
        function yesno($value,$message){
            if($value==null){
                $value="NO";
            }
            if($value!="SI" && $value!="NO"){
                array_push($this->details,$message);
            }
        }
        function isGuid($value,$label){
            $state=false;
            if(isset($value)){
                $guid = strtoupper($value);
                if (preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid)) {
                    $state=true;
                } 
            }
            if(!$state){
                array_push($this->details,"El campo $label no es válido");
            }
        }
        function exists($table,$id,$requerid,$message){
            $state=true;
            if($requerid){
                if(!isset($id) || strlen($id)==0){
                    array_push($this->details,$message);
                    $state=false;
                }
            }
            else{
                if(!isset($id) || strlen($id)==0){ $state=false;}
            }
            if($state){
                $db= new DataBaseManager();
                $sql="select id,activo from $table where id='".$id."'";
                $dt = $db->reader($sql);
                $omessage="";
                if(count($dt)>0){
                    $row=$dt[0];
                    if($row["activo"]==0){ $omessage=$message;}
                }
                else{
                    $omessage=$message;
                }
                unset($db);
                if(strlen($omessage)>0){
                    array_push($this->details,$omessage);
                }
            }
        }
        function process(){
            $result=new Result();
            
            $result->success=count($this->details)>0?false:true;
            if(!$result->success){
                $message="No se pudo procesar tu solicitud";
                $result->error= new ResultError($message,$this->details);
                echo json_encode($result);
                exit();
            }  
        }
    }

    class Result{
        public $success;  
        public $status;
        public $message;   
        public $data;
        public $error;

        public function __construct(){
            $this->status=200;
        }
    }

    class ResultError{
        public $message;
        public $details;
        public function __construct($message,$details){
            $this->message=$message;
            $this->details=$details;
        }
 
    }
?>