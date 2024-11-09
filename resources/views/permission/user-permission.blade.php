@extends('layouts.template')

@section('title', 'User Permission')

@section('content')
    <div class="row">
        @if (session('message'))
            @include('components.flash', [
                'type' => session('message')['type'],
                'text' => session('message')['text'],
            ])
        @endif

        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('permission.updateUserPermission') }}" method="post">
                        @method('PUT')
                        @csrf
                        <label for="user-select">Pilih user</label>
                        <select id="user-select" class="form-control" name="user-select" required>
                            <option value="" selected disabled>Select a user</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                        <div class="mt-3">

                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Assigned Permission</h4>
                                    <div id="active-permission">
                                        <!-- User details will be displayed here -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4>Open Permission</h4>
                                    <div id="open-permission">

                                    </div>
                                </div>

                            </div>
                            <div class="d-flex justify-content-end">
                                <button id="btn-submit" class="btn btn-primary" disabled type="submit">Assign
                                    Permission</button>
                            </div>

                        </div>
                </div>
                <!-- end card body -->
                </form>
            </div>
        </div>
    </div>

    @push('custom-script')
        <script src="{{ asset('assets/custom/js/master/permission/user-permission.js') }}">

        </script>
    @endpush
@endsection
