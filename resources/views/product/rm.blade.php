@extends('layouts.template')

@section('title', 'List Produk CI')

@section('content')
    <div class="mt-5">
        <table class="table table-bordered w-100" id="product-table">
            <thead>
                <tr>
                    <th>SAP Code</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th>Tanggal dibuat</th>                   
                  
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $item)
                    <tr>
                        <td> {{ $item->sap_code }} </td>
                        <td> {{ $item->sap_description }} </td>
                        <td> {{ $item->material_uom }} </td>
                        <td> {{ $item->created_at }} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    @push('custom-style')
        <link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">       
    @endpush
    @push('custom-script')
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
    let table = new DataTable('#product-table', {
                responsive: true,
                // order: [[5, 'desc']],
                scrollX:true,
            });
    </script>
    @endpush


        
   
@endsection
