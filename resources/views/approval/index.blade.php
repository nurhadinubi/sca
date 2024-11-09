@extends('layouts.template')

@section('title', 'List Approval')

@section('content')
    <div class="row">
        @if (session('message'))
            @include('components.flash', [
                'type' => session('message')['type'],
                'text' => session('message')['text'],
            ])
        @endif

        <div class="col">
            <div class="mb-3 d-flex justify-content-end">
                <a href="{{ route('master.approval.create') }}" class="btn btn-primary inline-block ml-auto"> <i
                        class="fa fa-plus-circle"></i> Tambah Approval</a>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Transaksi</th>
                                    <th>Departement</th>
                                    <th>Level</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($approvals as $item)
                                    <tr>

                                        <td>{{ $item->transaction_type }}</td>
                                        <td>{{ $item->doctype . ' - ' . $item->description }}</td>
                                        <td>{{ $item->level }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            @if ($item->is_deleted)
                                                <span class="badge rounded-pill text-bg-dark">Deleted</span>
                                            @endif
                                            @if ($item->is_active)
                                                <span class="badge rounded-pill text-bg-info">Aktif</span>
                                            @else
                                                <span class="badge rounded-pill text-bg-dark">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('master.approval.edit', ['id' => $item->id]) }}"
                                                class="btn btn-sm btn-outline-primary"> <i class="fa fa-cog"></i> Manage</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- end card body -->
                {{ $approvals->links() }}
            </div>
        </div>
    </div>
@endsection
