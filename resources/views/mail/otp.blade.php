<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Approval Request</title>
</head>
<body>
  <p style="margin-bottom: 2rem">Dear Bapak/Ibu ,</p>
  <p>Berikut ini kode Rahasia untuk akses </p>
  <table>
    <tbody>
      <tr>
        <td>Jenis Transaksi </td>
        <td>: {{$data->permission_name}}</td>
      </tr>
      <tr>
        <td>Waktu</td>
        <td>: {{\Carbon\Carbon::parse($data->created_at)->format('d-M-Y H:m:s')}}</td>
      </tr>
      {{-- <tr>
        <td>Product </td>
        <td>: {{$data->material->material_code ." - ". $data->material->material_description}}</td>
      </tr> --}}
    </tbody>
  </table>
  <p><strong>{{ $data->otp }}</strong></p>
   <p>jangan pernah memberikan kode ini kepada siapaun</p>
</body>
</html>