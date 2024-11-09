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
                <a href="{{ route('division.create') }}" class="btn btn-outline-primary">Tambah Divisi</a>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>KODE</th>
                                    <th>DESKRIPSI</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($divisions as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->doctype }}</td>
                                        <td>{{ $item->description }}</td>
                                        
                                        <td>
                                            <a href="{{ route('division.edit', ['id' => $item->id]) }}"
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

            {{ $divisions->links() }}
        </div>
    </div>
@endsection
