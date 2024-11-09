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
  <p>{{ $data->intro}}</p>
  <table>
    <tbody>
      <tr>
        <td>No Dokumen </td>
        <td>: {{$data->doc_number}}</td>
      </tr>
      <tr>
        <td>Doc. Date </td>
        <td>: {{\Carbon\Carbon::parse($data->doc_date)->format('d-M-Y')}}</td>
      </tr>
      <tr>
        <td>Product </td>
        <td>: {{$data->material->material_code ." - ". $data->material->material_description}}</td>
      </tr>
    </tbody>
  </table>
  <p>{{ $data->message }}</p>
  <p>Anda dapat mebuka dokumen tersebut dengan <a href="{{ route('scaleup.show', ['id'=>base64_encode($data->doc_number)]) }}" target="_blank" rel="noopener noreferrer">klik link ini</a> </p>
  <p>Terimakasih</p>
</body>
</html>