<?php
    class VCaja extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->process();
        }
    }
?>