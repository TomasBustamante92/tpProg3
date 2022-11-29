<?php
require_once('./fpdf/fpdf.php');

class PDF extends Fpdf
{
    public function __construct($orientation = 'L', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    public function CargarTabla($informacion)
    {
        $this->SetFont('Arial','',14);
        $this->AddPage();
        $x = 90;
        $y = 8;

        foreach($informacion as $titulo)
        {
            $sum = 0;
            foreach($titulo as $clave => $valor)
            {
                if($sum == 0){
                    $this->Cell(30,$y,strtoupper($clave),1,0,"C");
                }
                else if($sum == 1){
                    $this->Cell(100,$y,strtoupper($clave),1,0,"C");
                }
                else{
                    $this->Cell(30,$y,strtoupper($clave),1,0,"C");
                }
                $sum++;
            }
            break;
        }
        $this->Ln();
        
        foreach($informacion as $fila)
        {
            $sum = 0;
            foreach($fila as $columna)
            {
                if($sum == 0){
                    $this->Cell(30,$y,$columna,1,0,'C');
                }
                else if($sum == 1){
                    $this->Cell(100,$y,$columna,1,0,'C');
                }
                else{
                    $this->Cell(30,$y,$columna,1,0,'C');
                }
                $sum++;
                // $this->Cell($x,$y,$columna,1,0,'C');
            }
            $this->Ln();
        }
    }
    
    public function CargarMensaje($mensaje)
    {
        $this->SetFont('Arial','',14);
        $this->AddPage();
        $this->Cell(40,10,$mensaje);
    }

    public function ImprimirPdf($nombreDelArchivo)
    {
        $this->Output();
        $this->Output('F', $nombreDelArchivo);
    }
}
?>