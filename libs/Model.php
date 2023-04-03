<?php
function now(){
    $now="DATE_ADD(NOW(), INTERVAL ".DB_HOUR_DIFF." HOUR)";
    return $now;
}
function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return strtolower($uuid);
    }
}
class Model{
    protected $db;

    function __construct() {
        $this->db=new DataBaseManager();
    }
     
    function save($table,$entity){
        $dtexist=$this->exist($table,$entity);
        if(count($dtexist)>0){
            $row=$dtexist[0];
            if($row["activo"]==1){
                $this->update($table,$entity);
            }
            else{
                $this->gotoError("No es un registro de $table válido");
            }
        }
        else{
            $this->insert($table,$entity);
        }
    }
    function saveWithExclude($table,$entity,$excludes){
        $dtexist=$this->exist($table,$entity);
        if(count($dtexist)>0){
            $row=$dtexist[0];
            if($row["activo"]==1){
                $this->updateWithExclude($table,$entity,$excludes);
            }
            else{
                $this->gotoError("No es un registro de $table válido");
            }
        }
        else{
            $this->insert($table,$entity);
        }
    }
    function update($table,$entity){
        $now=now();
        if(!isset($entity->id)){
            $this->gotoError("El objeto no tiene el id asignado");
        }
        else{
            if(!isGuid($entity->id)){
                $this->gotoError("El objeto no tiene el id válido");
            }
        }
        $userid=auth::user();
        $i=0;
        $sql="update ".$table." set ";
        foreach ($entity as $key =>$value)
        {
            
            if($key!="db" && $key!="id"){
                if($i>0){  $sql.=","; }
                $sql.=$key." = '".$value."'";
                $i++;
            }
        }
        $sql.=",usuario_modificacion='$userid',fecha_hora_modificacion=$now ";
        $sql.=" where id = '".$entity->id."' and activo=1";
        $this->db->execute($sql);
        $this->gotoSuccess("Registro de ".$table." actualizado con éxito",$entity->id);
    }
    function updateWithExclude($table,$entity,$excludes){
        $now=now();
        if(!isset($entity->id)){
            $this->gotoError("El objeto no tiene el id asignado");
        }
        else{
            if(!isGuid($entity->id)){
                $this->gotoError("El objeto no tiene el id válido");
            }
        }
        $userid=auth::user();
        $i=0;
        $sql="update ".$table." set ";
        foreach ($entity as $key =>$value)
        {
            
            if($key!="db" && $key!="id"){
                $stateExclude=false;
                foreach($excludes as $exclude){
                    if($key==$exclude){
                        $stateExclude=true;
                    }
                }
                if(!$stateExclude){
                    if($i>0){  $sql.=","; }
                    $sql.=$key." = '".$value."'";
                    $i++;
                }
            }
        }
        $sql.=",usuario_modificacion='$userid',fecha_hora_modificacion=$now ";
        $sql.=" where id = '".$entity->id."' and activo=1";
        $this->db->execute($sql);
        $this->gotoSuccess("Registro de ".$table." actualizado con éxito",$entity->id);
    }
    function insert($table,$entity){
        $now=now();
        $i=0;
        $userid=auth::user();
        $sql="insert into ".$table."(";
        foreach ($entity as $key =>$value){
            if($key!="db"){
                if($i>0){  $sql.=","; }
                $sql.=$key;
                $i++;
            }
        }
        $sql.=",activo,usuario_creacion,fecha_hora_creacion)";
        $sql.=" values(";
        $i=0;
        foreach ($entity as $key =>$value)
        {              
            if($key!="db"){
                if($i>0){  $sql.=","; }
                $sql.="'".$value."'";
                $i++;
            }
        }
        $sql.=",1,'$userid',$now)";
         $this->db->execute($sql);
         $this->gotoSuccess("Registro de ".$table." creado con éxito",$entity->id);
    }
    function delete($table,$entity){
        $now=now();
        if(!isset($entity->id)){
            $this->gotoError("El objeto no tiene el id asignado");
        }
        else{
            if(!isGuid($entity->id)){
                $this->gotoError("El objeto no tiene el id válido");
            }
        }
        $dtexist=$this->exist($table,$entity);
        if(count($dtexist)>0){
            $row=$dtexist[0];
            if($row["activo"]==1){
                $userid=auth::user();
                $sql="update $table set activo=0,usuario_modificacion='$userid',fecha_hora_modificacion=$now where id='".$entity->id."'";
                $this->db->execute($sql);
                $this->gotoSuccess("Registro de ".$table." eliminado con éxito","");
            }
            else{
                $this->gotoError("No es un registro de $table válido");
            }
        }
        else{
            $this->gotoError("No es un registro de $table válido");
        }        
    }
    function all($table,$entity){
        $i=0;
 
        $sql="select ";
 
        foreach ($entity as $key =>$value){
            if($key!="db"){
                if($i>0){  $sql.=","; }
                $sql.=$key;
                $i++;
            }
        }
        $sql.=" from $table where activo=1";
         $dt=$this->db->reader($sql);
         $this->gotoSuccessData($dt);
    }

    function sqlread( $sql){
         $dt=$this->db->reader($sql);
         $this->gotoSuccessData($dt);
    }
    function sqlGet( $sql,$id){
        if(!isGuid($id)){
            $this->gotoError("El registro no es válido 1");
        }
        $sql=str_replace("@id",$id,$sql);
        $dt=$this->db->reader($sql);
        if(count($dt)>0){
            $this->gotoSuccessData($dt[0]);
        }
        else{
            $this->gotoError("El registro no es válido2");
        }
   }
   function sqlgetrow( $sql){
        $dt=$this->db->reader($sql);
        if(count($dt)>0){
            return $dt[0];
        }
        else{
            return null;
        }
    }
    function sqldata( $sql){
        $dt=$this->db->reader($sql);
        return $dt;
    }
    function exist($table,$entity){
        $sql="select id,activo from $table where id='".$entity->id."'";
        $dt = $this->db->reader($sql);
        return $dt;
    }
    
    public function gotoError($message){
        $result=new Result();          
        $result->success=false;
        $result->error= new ResultError($message,null);
        echo json_encode($result);
        exit(); 
    }
    public function gotoErrorDetails($message,$details){
        $result=new Result();          
        $result->success=false;
        $result->error= new ResultError($message,$details);
        echo json_encode($result);
        exit(); 
    }
    public function gotoSuccess($message,$id){
        $result=new Result();          
        $result->success=true;
        $result->message= $message;
        $result->data=$id;
        echo json_encode($result);
        exit(); 
    }
    public function gotoSuccessData($data){
        $result=new Result();          
        $result->success=true;
        $result->data= $data;
        echo json_encode($result);
        exit(); 
    }

    function sqlInsert($table,$entity){
        $now=now();
        $i=0;
        $userid=auth::user();
        $sql="insert into ".$table."(";
        foreach ($entity as $key =>$value){
            if($key!="db"){
                if($i>0){  $sql.=","; }
                $sql.=$key;
                $i++;
            }
        }
        $sql.=",activo,usuario_creacion,fecha_hora_creacion)";
        $sql.=" values(";
        $i=0;
        foreach ($entity as $key =>$value)
        {              
            if($key!="db"){
                if($i>0){  $sql.=","; }
                $sql.="'".$value."'";
                $i++;
            }
        }
        $sql.=",1,'$userid',$now)";
        return $sql;
    }
    function sqlUpdateSum($table,$id,$field,$value){
        $now=now();
        $userid=auth::user();
        $now=now();
        $sql="update ".$table." set ";
        $sql.=" $field = $field + $value,usuario_modificacion='$userid',fecha_hora_modificacion=$now ";
        $sql.=" where id='$id'";
        return $sql;
    }
    function get($table,$id,$fields){
        $sql="select ";
        if($fields!=null && count($fields)>0){
            $index=0;
            foreach ($fields as $field)
            {
                if($index>0){
                    $sql.=",";
                }
                $sql.=$field;
            }
        }
        else{
            $sql.=" * ";
        }
        $sql.=" from $table where id='$id' ";
   
        $dt = $this->db->reader($sql);
        if(count($dt)==0){
            $this->gotoError("No es un registro válido");
        }
        else{

        }
        $row=$dt[0];
        return $row;
    }
    function validateBalance($saldo,$monto){
        if($monto>$saldo){
            $message="No tiene saldo suficiente para realizar esta operación";
            $this->gotoError($message);
        }
    }
}

?>
