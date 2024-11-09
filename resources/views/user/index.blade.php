@extends('layouts.template')

@section('title', 'List User')

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
                <a href="{{ route('user.create') }}" class="btn btn-outline-primary">Tambah User</a>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table mb-0">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    {{-- <th>Username</th> --}}
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            @if ($item->is_active)
                                                <span class="badge rounded-pill text-bg-info">Aktif</span>
                                            @else
                                                <span class="badge rounded-pill text-bg-dark">Nonaktif</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('user.edit', ['id' => $item->id]) }}"
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

            {{ $user->links() }}
        </div>
    </div>
@endsection
