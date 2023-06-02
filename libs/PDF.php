<?php
class PDF{
    private $pdf;
    private $fontsize=8;
    private $fontsize_title=14;
    private $fontname="Arial";
    private $hoy;
    function __construct(){
        date_default_timezone_set("America/Lima");  
        $this->hoy = "Fecha y Hora de Impresion: ".date("d/m/Y H:i:s");      
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
    }

    public function setTitle($title){    
        $this->pdf->SetFont($this->fontname,'',$this->fontsize);
        $this->pdf->Cell(190,6, $this->hoy, 0, 1, 'R');
        $this->pdf->SetFont($this->fontname,'B',$this->fontsize_title);
        $this->pdf->Cell(190, 6, $title, 0, 1, 'C');
        $this->pdf->Ln();
    }
    public function setLocal($name){
        $this->pdf->SetFont('Arial','B',$this->fontsize);
        $this->pdf->Cell(30, 6, "LOCAL:", 0, 0, 'L');
        $this->pdf->SetFont('Arial','',$this->fontsize);
        $this->pdf->Cell(160, 6, $name, 0, 0, 'L');
        $this->pdf->Ln();
    }

    public function setTable($data,$head,$field,$align,$width){
        $this->pdf->SetFont('Arial','B',$this->fontsize);
        for($i=0;$i<count($head);$i++){  $this->pdf->Cell($width[$i],7,$head[$i],1,0,'C'); }
        $this->pdf->SetFont('Arial','',$this->fontsize);
        $this->pdf->Ln();
        foreach($data as $row){
            for($i=0;$i<count($head);$i++){  
                $value="";
                if($field[$i]!="" && isset($row[$field[$i]])){
                    $value=$row[$field[$i]];
                }
                $this->pdf->Cell($width[$i],7,$value,1,0,$align[$i]); 
            }
            $this->pdf->Ln();
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