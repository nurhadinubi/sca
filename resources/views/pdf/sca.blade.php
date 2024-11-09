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

     
        @page {
            header: page-header;
            footer: page-footer;
        }
    </style>


</head>
{{-- @dd( asset('assets/images/logo-form.png')) --}}

<body>
   
     
        <htmlpageheader name="page-header">
            PT DAESANG
        </htmlpageheader>
        <main class="">
            
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
                        @for ($i = 0; $i < 30; $i++)
                            <tr>
                                {{-- <td>&nbsp; {{ $loop->iteration }}</td> --}}
                                <td>&nbsp; {{ $loop->iteration }}</td>
                                <td> &nbsp;{{ $item->material_code }}</td>
                                <td> &nbsp;{{ $item->material_description }}</td>
                                <td>&nbsp;{{ $item->qty }}</td>
                                <td>&nbsp;{{ $item->uom }}</td>
                                <td>&nbsp;{{ $item->remark }}</td>
                            </tr>
                        @endfor
                    @endforeach
                </tbody>
            </table>
        </main>
        <htmlpagefooter name="page-footer">
            Footwr
        </htmlpagefooter>



</body>

</html>
