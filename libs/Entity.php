<?php
    class Entity{
        function set($input){
            if($input!=null){
                foreach ($this as $key =>$value)
                {
                    foreach($input as $ikey => $ivalue) {
                        if($ikey==$key){
                            $this->$key=$ivalue;
                            break;
                        }
                    }
                }
            }
        }
    }
?>