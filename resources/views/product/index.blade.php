@extends('layouts.template')

@section('title', 'List Produk CI')

@section('content')
    <div class="mt-5">
        <!-- Filter -->
        <div class="mb-3 hidden">
            <label for="filter-product-type">Filter by Product Type</label>
            <select id="filter-product-type" class="form-control">
                <option value="">All</option>
               @foreach ($categories as $item)
               <option value="{{ $item->id }}"> {{ $item->description}} </option>
               @endforeach
                <!-- Add more options as needed -->
            </select>
        </div>

        <table class="table table-bordered" id="product-table">
            <thead>
                <tr>
                    <th>Product Type</th>
                    <th>Product Code</th>
                    <th>SAP Code</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th>Tanggal dibuat</th>                   
                    <th>Action</th>                   
                </tr>
            </thead>
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
        $(document).ready(function () {
                var table = $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.index') }}",
                    data: function (d) {
                        d.product_type = $('#filter-product-type').val();
                    }
                },
                columns: [
                    { data: 'product_type_description', name: 'product_type_description' },  // Alias yang sesuai dari query
                    { data: 'product_code', name: 'product_code' },
                    { data: 'sap_code', name: 'sap_code' },
                    { data: 'description', name: 'description' },
                    { data: 'uom', name: 'uom' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[0, 'desc']]
            });

            $('#filter-product-type').change(function () {
                table.draw();
            });
         });
    </script>
    @endpush


        
   
@endsection
