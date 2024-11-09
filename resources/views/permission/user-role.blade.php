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
                    <form action="{{ route('permission.updateUserRole') }}" method="post">
                        @method('PUT')
                        @csrf
                        <div class="form-group">
                            <label for="user-select">Pilih user</label>
                            <select id="user-select" class="form-select bg-white" name="user-select" required>
                                <option value="" selected disabled>Select a user</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <label for="role-select">Pilih user</label>
                            <select name="role" id="role-select" class="form-select bg-white">
                                <option value="" selected disabled>Select Role</option>
                                @foreach ($roles as $item)
                                    <option {{ old('role') == $item->name ? 'selected' : '' }} value="{{ $item->name }}">
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-end">
                                <button id="btn-submit" class="btn btn-primary" type="submit">Assign
                                    Role</button>
                            </div>
                        </div>
                </div>
                <!-- end card body -->
                </form>
            </div>
        </div>
    </div>
    @push('custom-script')
        <script>
            $(document).ready(function() {
                const oldUser = @json(old('user-select'));
                $('#user-select').select2({
                    placeholder: "Select a user",
                    theme: "bootstrap-5",
                    // dropdownParent: 'form',
                    ajax: {
                        url: '{{ route('users.search') }}',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: `${item.name} - ${item.email}`,
                                        id: item.id
                                    }
                                })
                            };
                        },
                        cache: true
                    }
                });

                if (oldUser) {
                    $.ajax({
                        url: '{{ route('users.search') }}',
                        data: {
                            id: oldUser
                        },
                        success: function(data) {
                            const user = data.find(user => user.id == oldUser);
                            const option = new Option(`${user.name} - ${user.email}`, user.id, true, true);
                            $('#user-select').append(option).trigger('change');
                        }
                    });
                }

                $('#user-select').on('select2:select', function(e) {
                    var userId = e.params.data.id;

                    $.ajax({
                        url: '{{ url('master/role/user') }}/' + userId,
                        type: 'GET',
                        success: function(response) {
                            var selectedRole = response.name;
                            $('#role-select').val(selectedRole).trigger('change');
                        }
                    });
                })

            });
        </script>
    @endpush
@endsection
