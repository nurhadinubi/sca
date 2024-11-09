<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Approval Request</title>
</head>

<body>

    @if ($data->type == 'keycode')
        <p style="margin-bottom: 2rem">Dear Bapak/Ibu ,</p>
        <p>{{ $data->intro }}</p>
        <table>
            <tbody>

                <tr>
                    <td>Product </td>
                    <td>: {{ $data->product_code . ' - ' . $data->product_description }}</td>
                </tr>

                <tr>
                    <td>Keteranagn </td>
                    <td>: {{ $data->remark }}</td>
                </tr>
            </tbody>
        </table>

        <p>Anda dapat melihat requset tersebut dengan klik link <a
                href="{{ route('keycode.approve', ['id' => Crypt::encryptString($data->id)]) }}" target="_blank"
                rel="noopener noreferrer">klik link ini</a> </p>
        <p>
                Untuk approve atau reject dokumen anda dapat membuka melalui link 
                <a
                    href="{{ Config::get('custom.IP') }}external/keycode/{{$data->link }}" target="_blank"
                    rel="noopener noreferrer">klik link ini</a>
            </p>
    @elseif($data->type == 'keycode-notif')
        <p style="margin-bottom: 2rem">Dear Bapak/Ibu ,</p>
        <p>{{ $data->intro }}</p>
        <table>
            <tbody>
                <tr>
                    <td>Product </td>
                    <td>: {{ $data->product_code . ' - ' . $data->product_description }}</td>
                </tr>
                <tr>
                    <td>Keycode </td>
                    <td>: {{ $data->key_code }}</td>
                </tr>
                <tr>
                    <td>Keteranagn </td>
                    <td>: {{ $data->remark }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p style="margin-bottom: 2rem">Dear Bapak/Ibu ,</p>
        <p>{{ $data->intro }}</p>
        <table>
            <tbody>
                <tr>
                    <td>No Dokumen </td>
                    <td>: {{ $data->doc_number }}</td>
                </tr>
                <tr>
                    <td>Doc. Date </td>
                    <td>: {{ \Carbon\Carbon::parse($data->doc_date)->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td>Product </td>
                    <td>: {{ $data->product_code . ' - ' . $data->material_description }}</td>
                </tr>
            </tbody>
        </table>



        @if ($data->type == 'request')
            <p>Anda dapat mebuka detail dokumen tersebut dengan <a
                    href="{{ route('scaleup.approve', ['id' => Crypt::encryptString($data->doc_number)]) }}" target="_blank"
                    rel="noopener noreferrer">Detail</a> </p>

            <p>
                Untuk approve atau reject dokumen anda dapat membuka melalui link 
                <a
                    href="{{ Config::get('custom.IP') }}external/{{$data->link }}" target="_blank"
                    rel="noopener noreferrer">klik link ini</a>
            </p>
        @elseif($data->type == 'notif')
            <p>Anda dapat mebuka dokumen tersebut dengan melalui apps </p>
        @endif
    @endif

    <p>Terimakasih</p>
</body>

</html>
