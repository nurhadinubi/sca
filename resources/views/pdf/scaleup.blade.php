<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $header->doc_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .container {
            margin: 0.5cm;
            padding: 0;
            border: 1px black solid;
            /* height: 28.6cm; */
        }

        .clear {
            clear: both;
        }

        .border {
            border: 1px black solid;
        }

        .border-horizon {
            border-left: 1px solid #000;
            /* Border di sebelah kiri */
            border-right: 1px solid #000;
            /* Border di sebelah kanan */
        }

        .text-center {
            text-align: center;
        }

        header {
            padding: 0;
            position: fixed;
            width: 95%;
        }

        .head table tr {
            line-height: 20px;
        }

        .head table td {
            padding: 2px 0;
        }


        .table-border,
        .table-border tr,
        .table-border td,
        .table-border th {
            border: 1px black solid;
            border-collapse: collapse;
        }

        .head {
            margin: 0 0 1mm 0;
            /* position: fixed;
            top: 0;
            left: 0;
            right: 0; */
            
            height: 2.1cm;
            border-bottom: 1px black solid;

        }

        .left {
            float: left;
            display: inline-block;
        }

        .left>img {
            padding: 3mm;
        }

        main {
            clear: both;
            padding: 0 10px;
            min-height: 20cm;
        }

        .wrapper-page {
            page-break-after: always;
        }

        .wrapper-page:last-child {
            page-break-after: avoid;
        }

        <style>@page {
            margin: 180px 50px;
        }

        /* #header {
            position: fixed;
            left: 0px;
            top: -180px;
            right: 0px;
            height: 150px;
            background-color: orange;
            text-align: center;
        } */
/* 
        #footer {
            position: fixed;
            left: 0px;
            bottom: 0px;
            right: 0px;
            height: 150px;
            background-color: lightblue;
        } */

        /* #footer .page:after {
            content: counter(page, upper-roman);
        } */
    </style>
    </style>


</head>
{{-- @dd( asset('assets/images/logo-form.png')) --}}

<body>
    <div class="container wrapper-page">
        <header style="margin-bottom: 14mm;">
            <div class="head">
                <div style="width:18%;" class="left">
                    <img src="{{ public_path('assets/images/logo-form.png') }}" alt="Logo" width="120mm"
                        height="auto">
                    <h4 style="margin-bottom: 2px" class="text-center">Form</h4>
                </div>
                <div style="width:50%; height: 2.1cm;" class="left">
                    <div style="border-left: #000 1px solid; border-right:  #000 1px solid; height: 100%">
                        <h5 style="height: 7mm; line-height: 6mm; border-bottom: #000 1px solid;" class="text-center">
                            PT. DAESANG AGUNG INDONESIA</h5>
                        <h3 style="line-height: 10mm" class="text-center">Formula Scale Up</h3>
                    </div>
                </div>
                <div style="width:32%" class="left">
                    <div style="border-bottom: #000 1px solid; height: 7mm;line-height: 6mm; padding-left: 1mm;">
                        <table width="100%">
                            <tr>
                                <td width="35%">No Dokumen</td>
                                <td>: &nbsp; {{ 'DAI-FRM/CI/001/011' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="height: 7mm;line-height: 6mm; padding-left: 1mm; border-bottom: #000 1px solid;">
                        <table width="100%">
                            <tr>
                                <td width="35%">Tanggal Efektif</td>
                                <td>: &nbsp; {{ '16 Januari 2024' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div style=" height: 7mm;line-height: 6mm; padding-left: 1mm; ">
                        <table width="100%">
                            <tr>
                                <td width="35%">Revisi</td>
                                <td>: &nbsp; {{ '03' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div style=" border-bottom: #000 1px solid; padding: 2mm 0;">
                <h1 style="font-size:15px" class="text-center">SAUS BBQ</h1>
            </div>

            <div style="padding: 2mm" class="clear">
                <div class="left" style="width: 60%">
                    <table width="100%">
                        <tr>
                            <td width="30%" style="padding: 0;">Kode Produk</td>
                            <td>: &nbsp; {{ '03' }}</td>
                        </tr>
                        <tr>
                            <td width="30%" style="padding: 0;">Tanggal Terbit</td>
                            <td>: &nbsp; {{ '12 Juni 2024' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="left" style="width: 40%">2</div>
            </div>
        </header>
        <main class="">
            <h4 style="margin-bottom: 2px">Komponen</h4>
            <table width="100%" class="table-border">
                <thead>
                    <th>No</th>
                    <th>Kode Material</th>
                    <th>Deskripsi</th>
                    <th>Qty</th>
                    <th>Uom</th>
                    <th>Remark</th>
                </thead>
                <tbody>
                    @foreach ($detail as $item)
                        {{-- @for ($i = 0; $i < 30; $i++) --}}
                        <tr>
                            {{-- <td>&nbsp; {{ $loop->iteration }}</td> --}}
                            <td>&nbsp; {{ $loop->iteration }}</td>
                            <td> &nbsp;{{ $item->material_code }}</td>
                            <td> &nbsp;{{ $item->material_description }}</td>
                            <td>&nbsp;{{ $item->qty }}</td>
                            <td>&nbsp;{{ $item->uom }}</td>
                            <td>&nbsp;{{ $item->remark }}</td>
                        </tr>
                        {{-- @endfor --}}
                    @endforeach
                </tbody>
            </table>
        </main>
        <footer id="footer">
            {{-- tambahin Flow approval seperti di menu show  --}}
            Lorem Lorem, ipsum dolor sit amet consectetur adipisicing elit. Ut consectetur tenetur sequi, ullam totam perspiciatis harum omnis officiis autem enim vel iusto numquam fugit vero? Natus porro eveniet voluptatem expedita?
        </footer>
    </div>


</body>

</html>
 