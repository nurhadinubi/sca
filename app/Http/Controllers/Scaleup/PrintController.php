<?php

namespace App\Http\Controllers\Scaleup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use TCPDF;

class CunstomScaleup extends TCPDF
{

	function Header()
	{
		$this->SetFont('helvetica', '', 10);
		$this->SetFillColor(255, 255, 255);
		// $this->SetFillColor(240, 240, 240);

		// Column widths
		$colWidths = [40, 60, 40, 40];

		// Row heights
		$rowHeight = 10;

		// Border setting for all cells
		$border = 1;

		// Kolom pertama: Logo dan teks "FORM"
		$this->SetXY(5, 10);
		$this->Image($_SERVER["DOCUMENT_ROOT"] . 'assets/images/logo-form.png', 5, 10, 40, null, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
		$this->SetXY(5, 30);
		$this->Cell($colWidths[0], $rowHeight, 'FORM', $border, 1, 'C', 1);

		// Kolom kedua: PT ABD dan Form Pengajuan (3 rows)
		$this->SetXY(50, 10);
		$this->Cell($colWidths[1], $rowHeight, 'PT ABD', $border, 1, 'C', 1);
		$this->SetXY(50, 20);
		$this->Cell($colWidths[1], $rowHeight * 2, 'Form Pengajuan', $border, 1, 'C', 1);

		// Kolom ketiga dan keempat: 3 baris standard
		$this->SetXY(100, 10);
		$this->Cell($colWidths[2], $rowHeight, 'Standard Row 1', $border, 1, 'C', 1);
		$this->SetXY(100, 20);
		$this->Cell($colWidths[2], $rowHeight, 'Standard Row 2', $border, 1, 'C', 1);
		$this->SetXY(100, 30);
		$this->Cell($colWidths[2], $rowHeight, 'Standard Row 3', $border, 1, 'C', 1);

		$this->SetXY(140, 10);
		$this->Cell($colWidths[3], $rowHeight, 'Standard Row 1', $border, 1, 'C', 1);
		$this->SetXY(140, 20);
		$this->Cell($colWidths[3], $rowHeight, 'Standard Row 2', $border, 1, 'C', 1);
		$this->SetXY(140, 30);
		$this->Cell($colWidths[3], $rowHeight, 'Standard Row 3', $border, 1, 'C', 1);
	}

	public function Footer()
	{
		$this->SetY(-50);
		$this->SetFont('helvetica', 'I', 8);

		// Footer Table
		$this->SetFillColor(240, 240, 240);
		$this->Cell(60, 10, 'Footer Row 1, Col 1', 1, 0, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 1, Col 2', 1, 0, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 1, Col 3', 1, 1, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 2, Col 1', 1, 0, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 2, Col 2', 1, 0, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 2, Col 3', 1, 1, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 3, Col 1', 1, 0, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 3, Col 2', 1, 0, 'C', 1);
		$this->Cell(60, 10, 'Footer Row 3, Col 3', 1, 1, 'C', 1);
	}
}

class PrintController extends Controller
{

	public function print($id)
	{
		try {
			$doc_number = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			dd($th);
		}

		$pdf = new CunstomScaleup();
		$pdf->SetMargins(5, 50, 5);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);


		$pdf->AddPage();
	}
}
