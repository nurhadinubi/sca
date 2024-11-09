@extends('layouts.template')

@section('title', 'List Draft Scale Up')
@section('content')

    <div class="row ">
        <div class="col-lg-10">
            <h3 class="card-label">List Draft Scale Up</h3>
        </div>
        <div class="col-12">
            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
        </div>        
    </div>
    <table id="example" class="table table-striped w-100" >
        <thead>
            <tr>
                <th>No Dokumen</th>
                <th>Material</th>
                {{-- <th>Tanggal Dokumen</th> --}}
                <th>Tanggal Terbit</th>
                <th>Tanggal Request</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- @dd($scaleup) --}}
            @if ($scaleup)
                @foreach ($scaleup as $item)
                    <tr>
                        <td>{{ $item->doc_number }}</td>
                        <td>{{$item->product_code." - ". $item->material_description }}</td>
                        {{-- <td>{{ \Carbon\Carbon::parse($item->doc_date)->format('d-m-Y') }}</td> --}}
                        <td>{{ \Carbon\Carbon::parse($item->issue_date)->format('d-m-Y') }}</td>
                        <td data-sort="{{ $item->created_at }}">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y, H:m:s') }}</td>
                        <td>
                            @if ($item->status == 'P')
                                <span class="badge text-bg-warning"> PENDING </span>
                            @elseif($item->status == 'A')
                                <span class="badge text-bg-info"> APPROVED </span>
                            @elseif($item->status == 'R')
                                <span class="badge text-bg-danger"> REJECTED </span>                                
                            @elseif($item->status == '')
                                <span class="badge text-bg-danger"> DRAFT </span>
                            @endif
                        </td>

                        <td class="">
                            <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Detail" href="{{ route('draft.scaleup.show', ['id' => $item->id]) }}"
                                aria-label="hidden"><i class="fa fa-eye"></i></a>
                            @if ($item->status == 'P')
														{{-- <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Edit" href="{{ route('scaleup.show', ['id' => base64_encode($item->doc_number)]) }}"
															aria-label="hidden"><i class="fa fa-edit"></i></a> --}}
                            @elseif($item->status == 'A')
														<a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Print" href="{{ route('scaleup.print', ['id' => base64_encode($item->doc_number)]) }}"
															aria-label="hidden"><i class="fa fa-print"></i></a>
     
                            @endif
                        </td>

                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- {{ $scaleup->links() }} --}}

    @push('custom-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
    
     let table = new DataTable('#example', {
            responsive: true,
            order: [[3, 'desc']],
            scrollX:true,
        });
    </script>
@endpush
@endsection
