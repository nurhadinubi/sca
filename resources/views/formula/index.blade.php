@extends('layouts.template')

@section('title', 'List Formula')
@section('content')

    <div class="row ">
        <div class="col-lg-10">
            <h3 class="card-label">Item List</h3>
        </div>
        <div class="col-12">
            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
        </div>
        {{-- <div class="col-lg-2">
            <a class="btn btn-primary" href="{{ route('scaleup.create') }}">
                <span class="fas fa-plus-circle"></span>&nbsp; Buat Scale Up
            </a>
        </div> --}}
    </div>


    <table id="example" class="table table-striped w-100" >
        <thead>
            <tr>
                {{-- <th>No</th> --}}
                <th>No Dokumen</th>
                <th>Material</th>
                <th>Tanggal Dokumen</th>
                <th>valid From</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- @dd($scaleup) --}}
            @if ($formula)
                @foreach ($formula as $item)
                    <tr>
                        <td>{{ $item->doc_number }}</td>
                        <td>{{$item->material_code." - ". $item->material_description }}</td>
                        {{-- <td>{{ $item->material_code }}</td> --}}
                        <td>{{ \Carbon\Carbon::parse($item->doc_date)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->valid_date)->format('d-m-Y') }}</td>
                        <td>
                            @if ($item->status == 'P')
                                <span class="badge text-bg-warning"> PENDING </span>
                            @elseif($item->status == 'A')
                                <span class="badge text-bg-info"> APPROVED </span>
                            @elseif($item->status == 'R')
                                <span class="badge text-bg-danger"> REJECTED </span>
                            @endif
                        </td>

                        <td class="">
                            <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Detail" href="{{ route('sf.show', ['id' => base64_encode($item->doc_number)]) }}"
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

    {{ $formula->links() }}
@endsection
