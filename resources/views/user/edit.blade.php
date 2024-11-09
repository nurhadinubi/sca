@extends('layouts.template')

@section('title', 'EDIT User')

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

                    <form class="row g-3 needs-validation" autocomplete="off" method="POST"
                        action="{{ route('user.update', ['id' => $user->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control  @error('name') is-invalid	@enderror" id="name"
                                name="name" value="{{ old('name') ? old('name') : $user->name }}"
                                placeholder="Nama Lengkap" />
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid	@enderror" id="email"
                                name="email" value="{{ old('email') ? old('email') : $user->email }}" autocomplete="off"
                                placeholder="Email @" />
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid	@enderror">
                                @foreach ($roles as $item)
                                    <option {{ $user->hasRole($item->name) ? 'selected' : '' }} value="{{ $item->name }}">
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label @error('divisions') is-invalid	@enderror">Divisi</label>
                            @error('divisions')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            @foreach ($divisions as $item)
                                @php
                                    $isChecked = collect($assignedDivision)->contains('sub_category_id', $item->id);
                                    $isCheckedOld = old('divisions') && in_array($item->id, old('divisions'));
                                @endphp

                                <div>
                                    <input type="checkbox" id="division{{ $item->id }}" name="divisions[]"
                                        value="{{ $item->id }}" {{ $isChecked || $isCheckedOld ? 'checked' : '' }}>
                                    <label
                                        for="division{{ $item->id }}">{{ $item->description . ' - ' . $item->description }}</label>
                                </div>
                            @endforeach
                            {{-- <select name="division" id="division"
                                class="form-select @error('division') is-invalid	@enderror">
                                <option value="">Approval</option>
                                @foreach ($divisions as $item)
                                    <option {{ $item->id == $user->sub_category_id?'selected':'' }} value="{{ $item->id }}">{{ $item->code . ' - ' . $item->description }}
                                    </option>
                                @endforeach
                            </select> --}}
                            @error('division')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                            <button name="action" value="delete" class="btn btn-outline-primary" type="submit"> {{ $user->is_active?"Non Aktifkan":"Aktifkan" }} </button>
                            <button name="action" value="update" class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
        </div>
    </div>
@endsection
