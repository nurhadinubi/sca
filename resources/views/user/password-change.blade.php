@extends('layouts.template')

@section('title', 'Tambah User')

@section('content')
    <div class="row">
        <div class="col">
            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif

            <div class="card">
                <div class="card-body">

                  <form method="POST" action="{{ route('password.change') }}">
                    @csrf
            
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Old Password</label>
                        <input type="password" name="old_password" id="old_password" class="form-control @error('old_password') is-invalid @enderror" required>
                        @error('old_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control @error('new_password_confirmation') is-invalid @enderror" required>
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
                </div>
                <!-- end card body -->
            </div>
        </div>
    </div>
@endsection
