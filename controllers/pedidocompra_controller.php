<?php
class pedidocompra extends Controller{
    public function __construct(){
        parent::__construct();
        parent::usingData("DPedidocompra");
        parent::usingEntity("EFiltro");
    }

    public function listar(){
        http::role(PEDIDO_COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompra();
        $d->listar($o);
    }
    public function listarPedidoCompra(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompra();
        $d->listarPedidoCompra($o);
    }
    public function obtener(){
        http::role(PEDIDO_COMPRA);
        http::post();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompra();
        $d->obtener($o);
    }
    public function obtenerFiltros(){
        http::role(PEDIDO_COMPRA);
        http::post();
        $d=new DPedidocompra();
        $d->obtenerFiltros();
    }
    public function listarRegistrado(){
        http::role(PEDIDO_COMPRA_TOMA);
        http::post();
        $d=new DPedidocompra();
        $d->listarRegistrado();
    }
    public function tomarPedido(){
        http::role(PEDIDO_COMPRA_TOMA);
        http::put();
        $input=input();
        $o=new EFiltro($input);
        $d=new DPedidocompra();
        $d->tomarPedido($o);
    }

    public function aprobar(){
        http::role(PEDIDO_COMPRA_TOMA);
        http::put();
        $d=new DPedidocompra();
        $d->aprobar();
    }
    public function imprimirPedidos(){
        http::role(PEDIDO_COMPRA_TOMA);
        http::post();
        $this->imprimirPedidosEstado("REG");
    }
    public function imprimirPedidosAprobados(){
        http::role(COMPRA_REGISTRO);
        http::post();
        $this->imprimirPedidosEstado("APR");
    }
    private function imprimirPedidosEstado($estado){
        date_default_timezone_set("America/Lima");        
        $pdf = new FPDF();
        $pdf->AddPage();
         $d=new DPedidocompra();
        $dtProductos = $d->listarDetallePedidoTomado($estado);
        $dtPedidos = $d->listarPedidoTomado($estado);
 
        if(count($dtProductos)==0){
            $d->gotoError("No se ha tomado ningun pedido para imprimir");
        }
        
        $hoy = "Fecha y Hora de Impresion: ".date("d/m/Y H:i:s");
        $fontsize=8;
        
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(190, 6, $hoy, 0, 1, 'R');
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(190, 6, 'ORDEN DE COMPRA', 0, 1, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40, $fontsize, "CANT. PEDIDOS:", 0, 0, 'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(150, $fontsize, count($dtPedidos), 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetFont('Arial','',$fontsize);
        $head = array('NRO PEDIDO', 'FECHA','ESTADO','LOCAL','CREADO POR');
        $width= array(20, 30,20,80,40);
        $align=array('L', 'L', 'L','L','L');
        $field = array('numero', 'fecha_hora','estado_nombre','localidad_nombre','usuario_nombre');

        $pdf->SetFont('Arial','B',$fontsize);
        for($i=0;$i<count($head);$i++){  $pdf->Cell($width[$i],7,$head[$i],1,0,'C'); }
        $pdf->SetFont('Arial','',$fontsize);
        $pdf->Ln();
        foreach($dtPedidos as $ped){
            for($i=0;$i<count($head);$i++){  $pdf->Cell($width[$i],7,$ped[$field[$i]],1,0,$align[$i]); }
            $pdf->Ln();
        }

        $pdf->SetFont('Arial','',$fontsize);
        $head = array('DESCRIPCION', 'CANTIDAD');
        $width= array(160, 30);
        $align=array('L', 'R');
        $field = array('nombre', 'cantidad');

        $pdf->SetFont('Arial','B',$fontsize);
        $pdf->Cell(40, 6, "DETALLE", 0, 0, 'L');
        $pdf->Ln();

        for($i=0;$i<count($head);$i++){  $pdf->Cell($width[$i],7,$head[$i],1,0,'C'); }
        $pdf->Ln();
        $pdf->SetFont('Arial','',$fontsize);
        foreach($dtProductos as $row){
            for($i=0;$i<count($head);$i++){  $pdf->Cell($width[$i],7,$row[$field[$i]],1,0,$align[$i]); }
            $pdf->Ln();
        }
        foreach($dtPedidos as $ped){
            $pdf->AddPage();
            $pdf->SetFont('Arial','',$fontsize);
            $pdf->Cell(190, 6, $hoy, 0, 1, 'R');
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(190, 6, 'PEDIDO DE COMPRA', 0, 1, 'C');
            $pdf->Ln();

            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell(20, 6, "NUMERO:", 0, 0, 'L');
            $pdf->SetFont('Arial','',$fontsize);
            $pdf->Cell(30, 6, $ped["numero"], 0, 0, 'L');
            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell(30, 6, "FECHA DE PEDIDO:", 0, 0, 'L');
            $pdf->SetFont('Arial','',$fontsize);
            $pdf->Cell(35, 6, $ped["fecha_hora"], 0, 0, 'L');

            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell(20, 6, "ESTADO:", 0, 0, 'L');
            $pdf->SetFont('Arial','',$fontsize);
            $pdf->Cell(25, 6, $ped["estado_nombre"], 0, 0, 'L');

            $pdf->Ln();

            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell(30, 10, "LOCAL", 0, 0, 'L');
            $pdf->SetFont('Arial','',$fontsize);
            $pdf->Cell(160, 10, $ped["localidad_nombre"], 0, 0, 'L');
            $pdf->Ln();

            $pdf->SetFont('Arial','B',$fontsize);
            $pdf->Cell(30, 6, "CREADO POR:", 0, 0, 'L');
            $pdf->SetFont('Arial','',$fontsize);
            $pdf->Cell(160, 6, $ped["usuario_nombre"], 0, 0, 'L');

            
            $pdf->Ln();

            $detalle=$d->listarDetalle($ped["id"]);
            for($i=0;$i<count($head);$i++){  $pdf->Cell($width[$i],7,$head[$i],1,0,'C'); }
            $pdf->Ln();

            foreach($detalle as $row){
                for($i=0;$i<count($head);$i++){  $pdf->Cell($width[$i],7,$row[$field[$i]],1,0,$align[$i]); }
                $pdf->Ln();
            }

        }



        $pdfFile = $pdf->Output("","S");
        $base64String = chunk_split(base64_encode($pdfFile));
        $data["document"]=$base64String;
        $data["filename"]="Pedidos.pdf";
        http::gotoSuccess($data);
        
    }
}
?>