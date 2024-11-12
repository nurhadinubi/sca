<?php

namespace App\Http\Controllers;

use App\Models\KeyCode;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use TCPDF;



class TCPDFCustom extends TCPDF
{
    public $header_data;

    public function AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false)
    {
        // Call the parent method
        parent::AddPage($orientation, $format, $keepmargins, $tocpage);

        $margin = 5;
        $this->SetLineStyle([
            'width' => 0.1,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => [0, 0, 0]
        ]);

        // page dimensions
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();


        // Buat Border
        $this->Rect($margin, $margin + 2, $pageWidth - 2 * $margin, $pageHeight - 2 * $margin - 6);
    }

    public function Header()
    {
        Carbon::setLocale('id');
        $this->SetFont('helvetica', '', 10);
        $this->SetFillColor(255, 255, 255);

        // Column widths
        $colWidths = [40, 90, 30, 40];

        $margin = 5;
        // Row heights
        $rowHeight = 7;

        // Border setting for all cells
        $border = 1;

        $this->setCellPaddings(1, 0, 1, 0);

        // Kolom pertama: Logo dan teks "FORM"

        $this->SetXY(5, $rowHeight);
        $this->Cell($colWidths[0], $rowHeight * 2, null, $border, 1, 'C', 1);
        $this->SetXY(5, $rowHeight + 1);
        $this->Image($_SERVER["DOCUMENT_ROOT"] . 'assets/images/logo-form.png', 6, $rowHeight + 1, $colWidths[0] - 6, null, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
        $this->SetXY(5, $rowHeight * 3);
        $this->Cell($colWidths[0], $rowHeight, 'FORM', $border, 1, 'C', 1);

        // Kolom kedua: PT ABD dan Form Pengajuan (3 rows)
        $this->SetXY($colWidths[0] + $margin, $rowHeight);
        $this->Cell($colWidths[1], $rowHeight, 'PT. DAESANG AGUNG INDONESIA', $border, 1, 'C', 1);
        $this->SetXY($colWidths[0] + $margin, $rowHeight * 2);
        $this->Cell($colWidths[1], $rowHeight * 2, 'Formula Scale Up', $border, 1, 'C', 1);

        // Kolom ketiga dan keempat: 3 baris standard
        $this->SetXY(135, $rowHeight);
        $this->Cell($colWidths[2], $rowHeight, 'No. Dokumen', $border, 1, 'L', 1);
        $this->SetXY(135, $rowHeight * 2);
        $this->Cell($colWidths[2], $rowHeight, 'Tanggal Efektif', $border, 1, 'L', 1);
        $this->SetXY(135, $rowHeight * 3);
        $this->Cell($colWidths[2], $rowHeight, 'No Revisi', $border, 1, 'L', 1);

        $this->SetXY(165, $rowHeight);
        $this->Cell($colWidths[3], $rowHeight, ': DAI-FRM/CI/001/011', $border, 1, 'L', 1);
        $this->SetXY(165, $rowHeight * 2);
        $this->Cell($colWidths[3], $rowHeight, ': 16 Januari 2024', $border, 1, 'L', 1);
        $this->SetXY(165, $rowHeight * 3);
        $this->Cell($colWidths[3], $rowHeight, ': 03', $border, 1, 'L', 1);

        $this->SetXY(5, $rowHeight * 4);
        $this->Cell(array_sum($colWidths), $rowHeight, $this->header_data->material_description, $border, 1, 'C', 1);


        $this->SetXY(9, $rowHeight * 6);
        $this->Cell($colWidths[0], $rowHeight, 'Kode Produk', 0, 1, 'L', 1);
        $this->SetXY(9, $rowHeight * 7);
        $this->Cell($colWidths[0], $rowHeight, 'Tanggal Terbit', 0, 1, 'L', 1);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY($colWidths[0] + 10, $rowHeight * 6);
        $this->Cell($colWidths[0], $rowHeight, ': ' . $this->header_data->product_code, 0, 1, 'L', 1);
        $this->SetFont('helvetica', '', 10);
        $this->SetXY($colWidths[0] + 10, $rowHeight * 7);
        $this->Cell($colWidths[0], $rowHeight, ": " . Carbon::createFromFormat('Y-m-d', $this->header_data->issue_date)->translatedFormat('d F Y'), 0, 1, 'L', 1);

        $rightStart = array_sum(array_slice($colWidths, 0, 2)) + 4;
        $this->SetXY($rightStart, $rowHeight * 6);
        $this->Cell($colWidths[0], $rowHeight, 'Revisi', 0, 1, 'L', 1);
        $this->SetXY($rightStart, $rowHeight * 7);
        $this->Cell($colWidths[0], $rowHeight, 'Halaman', 0, 1, 'L', 1);
        $this->SetXY($rightStart + $colWidths[2], $rowHeight * 6);
        $this->Cell($colWidths[0], $rowHeight, ': ' . str_pad($this->header_data->revision, 2, '0', STR_PAD_LEFT) ?? str_pad(999, '2', '0', STR_PAD_LEFT), 0, 1, 'L', 1);
        $this->SetXY($rightStart + $colWidths[2], $rowHeight * 7);
        $this->Cell($colWidths[0], $rowHeight, ':' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 1, 'L', 1);
    }
    // Page footer
    public function Footer()
    {

        $this->SetFont('helvetica', 'B', 8);
        $this->SetFillColor(255, 255, 255);
        $colWidth = 66.6;
        $colHeiht = 6;
        // $x = 50;
        $x = 5;
        $y = -45;
        $border = 1;
        // Footer Table

        $data = [
            ["Dibuat Oleh : ", "Supervisor CI"],
            ["Disetujui Oleh :", "Ast Mgr/ Team Leader CI"],
            ["Diketahui Okeh :", "Direktur CI"],
        ];

        for ($i = 1; $i <= count($data); $i++) {
            $this->SetXY($x + $colWidth  * ($i - 1), $y);
            $this->Cell($colWidth, $colHeiht, $data[$i - 1][0], $border, 0, 'C', 1);
            $this->SetXY($x + $colWidth  * ($i - 1), $y + $colHeiht);
            $this->Cell($colWidth, $colHeiht * 3, '', $border, 0, 'C', 1);
            $this->SetXY($x + $colWidth  * ($i - 1), $y + $colHeiht * 4);
            $this->Cell($colWidth, $colHeiht, '', $border, 0, 'C', 1);
            $this->SetXY($x + $colWidth  * ($i - 1), $y + $colHeiht * 5);
            $this->Cell($colWidth, $colHeiht, $data[$i - 1][1], $border, 0, 'C', 1);
        }

        $this->setXY(5, (-11));
        $this->Cell(0, 10, 'Print Ke - ' . $this->header_data->print_count, 0, 0, 'L');
    }
}


class PDFController extends Controller
{
    public function scaleupWithKeyCode($id)
    {

        try {
            $data = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $keycode = KeyCode::where([
            'key_code' => $data->keycode,
            'is_used' => false,
            'status' => 'A',
            'user_id' => Auth::user()->id,
        ])->first();

        if (!$keycode) {
            return redirect()->back()->with(['message' => [
                "type" => "error",
                "text" => "Keycode yang anda gunakan tidak valid. keycode sudah digunakan atau user anda tidak mempunyai authorisasi untuk keycode ini"
            ]]);
        }

        $keycode->is_used = true;
        $keycode->save();
        $header = DB::table('scaleup_header')
            ->where('doc_number', $data->scaleup)
            ->where('user_id', Auth::user()->id)
            ->first();

        $itemCart = DB::table('scaleup_detail')->where('scaleup_header_id', $header->id)->get();
        $category_ref = $itemCart->pluck('category_reference')->toArray();
        $itemCategory = DB::table('item_category')->whereIn('uniqid', $category_ref)->get();

        $pdf = new TCPDFCustom();

        $pdf->SetProtection(array('modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'), '', null, 0, null);
        $pdf->header_data = $header;

        // Set dokumen informasi
        $pdf->SetCreator('PT. DAESANG AGUNG INDONESIA');
        $pdf->SetAuthor('PT. DAESANG AGUNG INDONESIA');
        $pdf->SetTitle('Print Scale Up ' . $header->product_code);
        $pdf->SetSubject('PDF Generation using TCPDF');

        $pdf->SetMargins(10, 65, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 54);

        // Set font
        $pdf->SetFont('helvetica', '', 9.5);

        // Define border margin (in mm)

        // Add a page
        // Tambahkan halaman baru
        $pdf->AddPage();

        // Inisialisasi grand total
        // $grandTotalQty = 0;
        // $grandTotalPercent = 0;

        // Mulai HTML dengan style CSS
        $css = '
            <style>
                table {
                    border-collapse: collapse;
                    width: 100%;
                    padding: 0.45px 4px;

                }
                th, td {
                    border: 0.2px solid #000;
                    
                    text-align: left;
                }
                th {
                    background-color: #ccc !important;
                    text-align: center;
                }
                .header-cell {
                    background-color: #eeeeee;
                    vertical-align:middle;
                    text-align:center;
                   
                }
                .rowspan-middle {
                   line-height:18px;
                }
                }
                .rowspan-single {
                   line-height:6px;
                }

                .subtotal-row {
                    font-weight: bold;
                }

                
            </style>
        ';

        $itemNumber = 1;

        // Iterasi melalui kategori item
        foreach ($itemCategory as $i) {
            $itemNumber = 1;
            // Tambahkan judul kategori
            $html = $css . '<h4>' . $i->description . '</h4>';
            // Mulai tabel
            $html .= '<table>
                 <thead>
                    <tr class="header-cell" >
                        <th class="rowspan-middle" width="5%" align="center" rowspan="2">NO</th>
                        <th class="rowspan-middle" width="12%" align="center" rowspan="2">Kode SAP</th>
                        <th class="rowspan-middle" width="30%" rowspan="2" align="center">NAMA BAHAN</th>
                        <th class="rowspan-single" width="20%" colspan="2" align="center">JUMLAH</th>
                        <th class="rowspan-middle" width="33%" rowspan="2" align="center">KETERANGAN</th>
                    </tr>
                    <tr class="header-cell">
                        <th class="rowspan-single" width="10%" align="center">Kg</th>
                        <th class="rowspan-single" width="10%" align="center">%</th>
                    </tr>
                 </thead>
                 <tbody>';

            $subtotalQty = 0;
            $subtotalPercent = 0;

            // Filter item berdasarkan kategori
            foreach ($itemCart as $item) {
                if ($item->category_reference == $i->uniqid) {
                    // for ($j = 0; $j < 9; $j++) {
                    $html .= '<tr>
                            <td width="5%" align="center">' . $itemNumber . '</td>
                            <td width="12%">' . $item->material_code . '</td>
                            <td width="30%">' . $item->material_description . '</td>
                            <td width="10%" align="right">' . number_format(round(($item->percent * $header->total / 100), 2), 3) . '</td>
                            <td width="10%" align="right">' . number_format($item->percent, 3) . ' </td>
                            <td width="33%">' . $item->remark . '</td>
                        </tr>';
                    $itemNumber++;
                    $subtotalQty += round(($item->percent * $header->total / 100), 2);
                    $subtotalPercent += $item->percent;
                    // }
                }
            }

            // Tambahkan subtotal per kategori
            $html .= '</tbody>
                        <tfoot>
                            <tr class="subtotal-row">
                                <td colspan="2" align="center">TOTAL</td>
                                <td></td>
                                <td align="right">' . number_format($subtotalQty, 3) . '</td>
                                <td align="right">' . number_format($subtotalPercent, 3) . '</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table><br>';

            // $grandTotalQty += $subtotalQty;
            // $grandTotalPercent += $subtotalPercent;

            // Write HTML ke PDF
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        // Tambahkan Grand Total
        // $html = $css . '
        //  <table>
        //      <tfoot>
        //          <tr class="subtotal-row">
        //              <td width="62%" colspan="2" align="center">Total Keseluruhan:</td>
        //              <td width="9%" align="right">' . number_format($grandTotalQty, 2) . '</td>
        //              <td width="9%" align="right">' . number_format($grandTotalPercent, 3) . '</td>
        //              <td width="20%"></td>
        //          </tr>
        //      </tfoot>
        //  </table>';

        // Write HTML ke PDF
        $html = '<p>Berat Perkemasan :' . $header->per_pack . '</p>
                 <p>Alasan Perubahan (Jika ada): ' . $header->remark . '</p>
                 ';

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->IncludeJS("print();");

        // Output PDF sebagai inline/ stream
        return $pdf->Output("Print Scaleup-" . $header->product_code . '.pdf', 'I');
    }
    public function scaleupPDF($id)
    {
        try {
            $doc_number = Crypt::decryptString($id);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $header = DB::table('scaleup_header')
            ->where('doc_number', $doc_number)
            ->where('user_id', Auth::user()->id)
            ->first();

        $itemCart = DB::table('scaleup_detail')->where('scaleup_header_id', $header->id)->get();
        $category_ref = $itemCart->pluck('category_reference')->toArray();
        $itemCategory = DB::table('item_category')->whereIn('uniqid', $category_ref)->get();

        $pdf = new TCPDFCustom();

        $pdf->SetProtection(array('modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'), '', null, 0, null);
        $pdf->header_data = $header;

        // Set dokumen informasi
        $pdf->SetCreator('PT. DAESANG AGUNG INDONESIA');
        $pdf->SetAuthor('PT. DAESANG AGUNG INDONESIA');
        $pdf->SetTitle('Print Scale Up ' . $header->product_code);
        $pdf->SetSubject('PDF Generation using TCPDF');

        $pdf->SetMargins(10, 65, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 54);

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Define border margin (in mm)

        // Add a page
        // Tambahkan halaman baru
        $pdf->AddPage();

        // Inisialisasi grand total
        $grandTotalQty = 0;
        $grandTotalPercent = 0;

        // Mulai HTML dengan style CSS
        $css = '
            <style>

                table {
                    border-collapse: collapse;
                    width: 100%;
                    padding: 0.9px 4px;

                }
                th, td {
                    border: 0.2px solid #000;
                    
                    text-align: left;
                }
                th {
                    background-color: #ccc !important;
                    text-align: center;
                }
                .header-cell {
                    background-color: #eeeeee;
                    vertical-align:middle;
                    text-align:center;
                   
                }
                .rowspan-middle {
                   line-height:18px;
                }
                }
                .rowspan-single {
                   line-height:6px;
                }

                .subtotal-row {
                    font-weight: bold;
                }

                
            </style>
        ';

        $itemNumber = 1;

        // Iterasi melalui kategori item
        foreach ($itemCategory as $i) {
            $itemNumber = 1;
            // Tambahkan judul kategori
            $html = $css . '<h4>' . $i->description . '</h4>';
            // Mulai tabel
            $html .= '<table>
                 <thead>
                    <tr class="header-cell" >
                        <th class="rowspan-middle" width="5%" align="center" rowspan="2">NO</th>
                        <th class="rowspan-middle" width="12%" align="center" rowspan="2">Kode SAP</th>
                        <th class="rowspan-middle" width="30%" rowspan="2" align="center">NAMA BAHAN</th>
                        <th class="rowspan-single" width="20%" colspan="2" align="center">JUMLAH</th>
                        <th class="rowspan-middle" width="33%" rowspan="2" align="center">KETERANGAN</th>
                    </tr>
                    <tr class="header-cell">
                        <th class="rowspan-single" width="10%" align="center">Kg</th>
                        <th class="rowspan-single" width="10%" align="center">%</th>
                    </tr>
                 </thead>
                 <tbody>';

            $subtotalQty = 0;
            $subtotalPercent = 0;

            // Filter item berdasarkan kategori
            foreach ($itemCart as $item) {
                if ($item->category_reference == $i->uniqid) {
                    // for ($j = 0; $j < 10; $j++) {
                    $html .= '<tr>
                            <td width="5%" align="center">' . $itemNumber . '</td>
                            <td width="12%">' . $item->material_code . '</td>
                            <td width="30%">' . $item->material_description . '</td>
                            <td width="10%" align="right">' . number_format(round(($item->percent * $header->total / 100), 2), 2) . '</td>
                            <td width="10%" align="right">' . number_format($item->percent, 2) . ' </td>
                            <td width="33%">' . $item->remark . '</td>
                        </tr>';
                    $itemNumber++;
                    $subtotalQty += round(($item->percent * $header->total / 100), 2);
                    $subtotalPercent += $item->percent;
                    // }
                }
            }

            // Tambahkan subtotal per kategori
            $html .= '</tbody>
                        <tfoot>
                            <tr class="subtotal-row">
                                <td colspan="2" align="center">TOTAL</td>
                                <td></td>
                                <td align="right">' . number_format($subtotalQty, 2) . '</td>
                                <td align="right">' . number_format($subtotalPercent, 2) . '</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table><br>';

            // $grandTotalQty += $subtotalQty;
            // $grandTotalPercent += $subtotalPercent;

            // Write HTML ke PDF
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        // Tambahkan Grand Total
        // $html = $css . '
        //  <table>
        //      <tfoot>
        //          <tr class="subtotal-row">
        //              <td width="62%" colspan="2" align="center">Total Keseluruhan:</td>
        //              <td width="9%" align="right">' . number_format($grandTotalQty, 2) . '</td>
        //              <td width="9%" align="right">' . number_format($grandTotalPercent, 3) . '</td>
        //              <td width="20%"></td>
        //          </tr>
        //      </tfoot>
        //  </table>';

        // Write HTML ke PDF
        $html = '<p>Berat Perkemasan :' . $header->per_pack . '</p>
                 <p>Alasan Perubahan : ' . $header->remark . '</p>
                 <p>Rev00 : ' . $header->rev0 . '</p>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->IncludeJS("print();");

        // Output PDF sebagai inline/ stream
        return $pdf->Output("Print Scaleup-" . $header->product_code . '.pdf', 'I');
    }

    // FORMULA SEMI FINISH
    public function formulaSemiFinishPDF($id)
    {
        try {
            $doc_number = Crypt::decryptString($id);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $header = DB::table('scaleup_header')
            ->where('doc_number', $doc_number)
            ->where('user_id', Auth::user()->id)
            ->first();

        $itemCart = DB::table('scaleup_detail')->where('scaleup_header_id', $header->id)->get();
        $category_ref = $itemCart->pluck('category_reference')->toArray();
        $itemCategory = DB::table('item_category')->whereIn('uniqid', $category_ref)->get();

        $pdf = new TCPDFCustom();

        $pdf->SetProtection(array('modify', 'copy', 'annot-forms', 'fill-forms', 'extract', 'assemble', 'print-high'), '', null, 0, null);
        $pdf->header_data = $header;

        // Set dokumen informasi
        $pdf->SetCreator('PT. DAESANG AGUNG INDONESIA');
        $pdf->SetAuthor('PT. DAESANG AGUNG INDONESIA');
        $pdf->SetTitle('Print Scale Up ' . $header->product_code);
        $pdf->SetSubject('PDF Generation using TCPDF');

        $pdf->SetMargins(10, 65, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 54);

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Define border margin (in mm)

        // Add a page
        // Tambahkan halaman baru
        $pdf->AddPage();

        // Inisialisasi grand total
        $grandTotalQty = 0;
        $grandTotalPercent = 0;

        // Mulai HTML dengan style CSS
        $css = '
            <style>

                table {
                    border-collapse: collapse;
                    width: 100%;
                    padding: 0.9px 4px;

                }
                th, td {
                    border: 0.2px solid #000;
                    
                    text-align: left;
                }
                th {
                    background-color: #ccc !important;
                    text-align: center;
                }
                .header-cell {
                    background-color: #eeeeee;
                    vertical-align:middle;
                    text-align:center;
                   
                }
                .rowspan-middle {
                   line-height:18px;
                }
                }
                .rowspan-single {
                   line-height:6px;
                }

                .subtotal-row {
                    font-weight: bold;
                }

                
            </style>
        ';

        $itemNumber = 1;

        // Iterasi melalui kategori item
        foreach ($itemCategory as $i) {
            $itemNumber = 1;
            // Tambahkan judul kategori
            $html = $css . '<h4>' . $i->description . '</h4>';
            // Mulai tabel
            $html .= '<table>
                 <thead>
                    <tr class="header-cell" >
                        <th class="rowspan-middle" width="5%" align="center" rowspan="2">NO</th>
                        <th class="rowspan-middle" width="12%" align="center" rowspan="2">Kode SAP</th>
                        <th class="rowspan-middle" width="30%" rowspan="2" align="center">NAMA BAHAN</th>
                        <th class="rowspan-single" width="20%" colspan="2" align="center">JUMLAH</th>
                        <th class="rowspan-middle" width="33%" rowspan="2" align="center">KETERANGAN</th>
                    </tr>
                    <tr class="header-cell">
                        <th class="rowspan-single" width="10%" align="center">Kg</th>
                        <th class="rowspan-single" width="10%" align="center">%</th>
                    </tr>
                 </thead>
                 <tbody>';

            $subtotalQty = 0;
            $subtotalPercent = 0;

            // Filter item berdasarkan kategori
            foreach ($itemCart as $item) {
                if ($item->category_reference == $i->uniqid) {
                    // for ($j = 0; $j < 10; $j++) {
                    $html .= '<tr>
                            <td width="5%" align="center">' . $itemNumber . '</td>
                            <td width="12%">' . $item->material_code . '</td>
                            <td width="30%">' . $item->material_description . '</td>
                            <td width="10%" align="right">' . number_format(round(($item->percent * $header->total / 100), 2), 2) . '</td>
                            <td width="10%" align="right">' . number_format($item->percent, 2) . ' </td>
                            <td width="33%">' . $item->remark . '</td>
                        </tr>';
                    $itemNumber++;
                    $subtotalQty += round(($item->percent * $header->total / 100), 2);
                    $subtotalPercent += $item->percent;
                    // }
                }
            }

            // Tambahkan subtotal per kategori
            $html .= '</tbody>
                        <tfoot>
                            <tr class="subtotal-row">
                                <td colspan="2" align="center">TOTAL</td>
                                <td></td>
                                <td align="right">' . number_format($subtotalQty, 2) . '</td>
                                <td align="right">' . number_format($subtotalPercent, 2) . '</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table><br>';

            // Write HTML ke PDF
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        // Write HTML ke PDF
        $html = '<p>Berat Perkemasan :' . $header->per_pack . '</p>
                 <p>Alasan Perubahan : ' . $header->remark . '</p>
                 <p>Rev00 : ' . $header->rev0 . '</p>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->IncludeJS("print();");

        // Output PDF sebagai inline/ stream
        return $pdf->Output("Print Scaleup-" . $header->product_code . '.pdf', 'I');
    }

}
