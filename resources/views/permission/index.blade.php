@extends('layouts.template')

@section('title', 'List Permission')

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
                        class="fa fa-plus-circle"></i> Create Permission</a>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    
                                    <th>Permission</th>
                                    <th>Tanggal Create</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($permissions as $item)
                                    <tr>                                       
                                        <td>{{ $item->name }}</td>
                                        <td>{{ \Carbon\carbon::parse($item->created_at)->format('d-M-Y H:m:s') }}</td>
                                        
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
                {{ $permissions->links() }}
            </div>
        </div>
    </div>
@endsection
