<?php
    class Entity{
        function set($input){
            if($input!=null){
                foreach ($this as $key =>$value)
                {
                    foreach($input as $ikey => $ivalue) {
                        if($ikey==$key){
                            $this->$key=$this->formatedValue($ivalue);
                            break;
                        }
                    }
                }
            }
        }
        private function formatedValue($value){
            $chars=["for(","cast(","decimal","float","bigint","varchar"," char(",
            "'","=","select ","select","avg","count",">","<"," in ","(",")","order ",
            "bucle","switch","drop","alter","left","right","mysql","sql",
            "limit ","from ","convert"," or "," and "];
            foreach($chars as $char){
                $value=str_ireplace($char,"",$value);    
            }
            return $value;
        }

         
    }

 ?>