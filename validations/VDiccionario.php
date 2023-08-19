<?php
    class VDiccionario extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->dice,"El campo dice es obligatorio");
            $this->required($o->quizodecir,"El campo quizo decir es obligatorio");
            $this->process();
        }
    }
?>