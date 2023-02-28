<?php
    class VProducto extends Validator{
        
        function validate($o){
            $this->isGuid($o->id,"id");
            $this->required($o->nombre, "nombre");
            $this->required($o->stock, "stock");
            $this->yesno($o->stock, "El check Stock no es válido");
            $this->process();
        }
    }
?>