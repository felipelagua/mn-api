<?php
class PDF{
    private $pdf;
    private $fontsize=7;
    private $fontsize_title=13;
    private $fontname="Arial";
    private $hoy;
    private $title;
    private $local="";
    function __construct(){
        date_default_timezone_set("America/Lima");  
        $this->hoy = "Fecha y Hora de Impresion: ".date("d/m/Y H:i:s");      
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
    }
    public function setTitle($value){    
        $this->title=$value;
    }
    public function setLocal($value){
        $this->local=$value;
    }
    public function printTitle(){    
        $this->pdf->SetFont($this->fontname,'',$this->fontsize);
        $this->pdf->Cell(190,6, $this->hoy, 0, 1, 'R');
        $this->pdf->SetFont($this->fontname,'B',$this->fontsize_title);
        $this->pdf->Cell(190, 6, $this->title, 0, 1, 'C');
        $this->pdf->Ln();
    }
    public function printLocal(){
        if($this->local!=""){
            $this->pdf->SetFont('Arial','B',$this->fontsize);
            $this->pdf->Cell(30, 6, "LOCAL:", 0, 0, 'L');
            $this->pdf->SetFont('Arial','',$this->fontsize);
            $this->pdf->Cell(160, 6, $this->local, 0, 0, 'L');
            $this->pdf->Ln();
        }
    }

    public function setTable($data,$head,$field,$align,$width){
        header('Content-Type: text/html; charset=utf8');
       $maxrows=33;
        $numpage=1;
        $line=0;
        $index=0;
        $count=count($data);
        foreach($data as $row){
            if($line==$maxrows){ $line=0; $numpage++;$this->pdf->AddPage();}
            if($line==0){
                $this->printTitle();
                $this->printLocal();
                $this->pdf->SetFont('Arial','B',$this->fontsize);
                for($i=0;$i<count($head);$i++){  $this->pdf->Cell($width[$i],7,$head[$i],1,0,'C'); }
                $this->pdf->SetFont('Arial','',$this->fontsize);
                $this->pdf->Ln();
            }
            for($i=0;$i<count($head);$i++){  
                $value="";
                if($field[$i]!="" && isset($row[$field[$i]])){
                    $value=$row[$field[$i]];
                }
                $this->pdf->Cell($width[$i],7,$value,1,0,$align[$i]); 
            }
            $this->pdf->Ln();
            if($line==$maxrows - 1  || $index==$count-1 ){
                $this->pdf->SetFont('Arial','B',8);
                $this->pdf->Cell(190, 6, "Pag. ".$numpage, 0, 0, 'R');
            }
            $line++;
            $index++;
        }
    }


    public function setTableCategory($data,$data2,$head,$field,$align,$width){
        header('Content-Type: text/html; charset=utf8');
       $maxrows=33;
        $numpage=1;
        $line=0;
        $index=0;
        $count=count($data);
        foreach($data as $row){
            if($line==$maxrows){ $line=0; $numpage++;$this->pdf->AddPage();}
            if($line==0){
                $this->printTitle();
                $this->printLocal();
                $this->pdf->SetFont('Arial','B',$this->fontsize);
                for($i=0;$i<count($head);$i++){  $this->pdf->Cell($width[$i],7,$head[$i],1,0,'C'); }
                $this->pdf->SetFont('Arial','',$this->fontsize);
                $this->pdf->Ln();
            }
            for($i=0;$i<count($head);$i++){  
                $value="";
                if($field[$i]!="" && isset($row[$field[$i]])){
                    $value=$row[$field[$i]];
                }
                $this->pdf->Cell($width[$i],7,$value,1,0,$align[$i]); 
            }
            $this->pdf->Ln();
            if($line==$maxrows - 1  || $index==$count-1 ){
                $this->pdf->SetFont('Arial','B',8);
                $this->pdf->Cell(190, 6, "Pag. ".$numpage, 0, 0, 'R');
            }
            $line++;
            $index++;
        }
    }


    public function download($name){
        $pdfFile = $this->pdf->Output("","S");
        $base64String = chunk_split(base64_encode($pdfFile));
        $data["document"]=$base64String;
        $data["filename"]=$name.".pdf";
        http::gotoSuccess($data);
    }
}

?>