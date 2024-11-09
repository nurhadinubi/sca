@extends('layouts.template')

@section('title', 'Tambah User')

@section('content')
    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-body">

                    <form class="row g-3 needs-validation" autocomplete="off" method="POST" action="{{ route('user.store') }}">
                        @csrf
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control  @error('name') is-invalid	@enderror" id="name"
                                name="name" value="{{ old('name') ? old('name') : '' }}" placeholder="Nama Lengkap" />
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid	@enderror" id="email"
                                name="email" value="{{ old('email') ? old('email') : '' }}" autocomplete="off" placeholder="Email @" />
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid	@enderror"
                                id="password" name="password" value="" autocomplete="off" placeholder="password" />
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid	@enderror">
                                @foreach ($roles as $item)
                                    <option {{ $item->name == 'need-otp' ? 'selected' : '' }} value="{{ $item->name }}">
                                        {{ ($item->name=='need-otp') ?'Creator' : $item->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="division" class="form-label">Divisi</label>
                            <select name="division" id="division"
                                class="form-select @error('division') is-invalid	@enderror">
                                <option value="nullable">Approval</option>
                                @foreach ($divisions as $item)
                                    <option value="{{ $item->id }}">{{ $item->code . ' - ' . $item->description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
        </div>
    </div>
@endsection
