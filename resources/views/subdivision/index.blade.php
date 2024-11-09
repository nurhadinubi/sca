@extends('layouts.template')

@section('title', 'List Divison')

@section('content')
    <div class="row">
        <div class="col">
            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
            <div class="mb-3">
                <a href="{{ route('sub-div.create') }}" class="btn btn-outline-primary">Tambah Sub Divisi</a>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Divisi</th>
                                    <th>Kode</th>
                                    <th>Sub Divisi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subs as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->doctype }}</td>
                                        <td>{{ $item->code }}</td>
                                        <td>{{ $item->description }}</td>
                                        
                                        <td>
                                            <a href="{{ route('sub-div.edit', ['id' => $item->id]) }}"
                                                class="btn btn-sm btn-outline-secondary">Edit</a>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end card body -->
            </div>

            {{ $subs->links() }}
        </div>
    </div>
@endsection
