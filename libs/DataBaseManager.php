<?php
if (!function_exists('str_contains')) {
    function str_contains (string $haystack, string $needle)
    {
        return empty($needle) || strpos($haystack, $needle) !== false;
    }
}

class DataBaseManager{
    private $dbh;
    private function open(){
        try {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->dbh = new PDO($dsn, DB_USER, DB_PASS);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    private function close(){
       $this->dbh=null;
    }
    public function execute($sql){
        $this->open();
        try{         
             $this->dbh->exec($sql);
            $this->close();
        }
        catch(PDOException $ex){
            $this->close();
            $this->gotoError($ex->getMessage());
        }
        catch(Exception $ex){
            $this->close();
            $this->gotoError($ex->getMessage());
        }
    }
    public function transac($arrasql){
        $this->open();
        try{          
            $this->dbh->beginTransaction();

            foreach($arrasql as $sql){
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
            }

            if ($this->dbh->inTransaction()) {
                $this->dbh->commit();
            }
            $this->close();
            $message="Operación realizada con éxito";
            $this->gotoSuccess($message);
        }
        catch(PDOException $ex){
            $this->dbh->rollBack();
            $this->close();
            $this->gotoError($ex->getMessage());
        }
        catch(Exception $ex){
            $this->close();
            $this->gotoError($ex->getMessage());
        }
    }

    public function reader($sql){
        $this->open();
        try{         
             $stmt = $this->dbh->prepare($sql);
             $stmt->setFetchMode(PDO::FETCH_ASSOC);
             $stmt->execute();
             $result=$stmt->fetchAll(PDO::FETCH_ASSOC);     
             $this->close();
             return $result;
        }
        catch(PDOException $ex){
            $this->close();
            $this->gotoError($ex->getMessage());
        }
        catch(Exception $ex){
            $this->close();
            $this->gotoError($ex->getMessage());
        }
    }
   
    private function gotoError($message){
        $result=new Result();          
        $result->success=false;
        $result->error= new ResultError($message,null);
        echo json_encode($result);
        exit(); 
    }
    private function gotoSuccess($message){
        $result=new Result();          
        $result->success=true;
        $result->message=$message;
        echo json_encode($result);
        exit(); 
    }
}
 ?>
