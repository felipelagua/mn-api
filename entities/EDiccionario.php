<?php
    class EDiccionario extends Entity{
        public $id;
        public $dice;
        public $quizodecir;
        public function __construct($input) { 
            if($input!=null){
                $this->set($input);
            }
            else{
                $this->dice="";
                $this->quizodecir="";
            }
            if(!isset($this->id) || $this->id==null || $this->id==""){
                $this->id=guid();
            } 
        }
    }
?>