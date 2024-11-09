@extends('layouts.template')

@section('title', 'List Produk CI')

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
                <a href="{{ route('ci.create') }}" class="btn btn-outline-primary">Tambah Produk CI</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode Produk</th>
                                    <th>Deskripsi</th>
                                    <th>Unit</th>
                                    <th>Kode SAP</th>
                                    <th>Kategori</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->uom }}</td>
                                        <td>{{ $item->sap_code }}</td>
                                        <td>
                                            @if ($item->is_active)
                                                <span class="badge rounded-pill text-bg-info">Aktif</span>
                                            @else
                                                <span class="badge rounded-pill text-bg-dark">Nonaktif</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('ci.edit', ['id' => $item->id]) }}"
                                                class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @role('admin')
                                                <a href="{{ route('password.reset', $item->id) }}" class="btn btn-sm btn-outline-secondary">Reset Password</a>
                                            @endrole
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end card body -->
            </div>

            {{ $products->links() }}
        </div>
    </div>
@endsection
