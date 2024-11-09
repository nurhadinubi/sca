@extends('layouts.template')

@section('title', 'Create Approval')

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
                    {{-- @dd(session('message')) --}}
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('master.approval.store') }}">
                        @csrf
                        <div class="col-md-6">
                            <label for="transaction_type" class="form-label">Tipe Transaksi</label>
                            <select name="transaction_type" id="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror"
                                required>
                                <option value="scaleup-create">Buat Scaleup</option>
                                <option value="scaleup-edit">Edit Scaleup</option>
                                <option value="scaleup-print">Cetak Scaleup</option>
                                <option value="scaleup-view">Lihat Scaleup</option>
                                <option value="formula-create">Buat Formula</option>
                                <option value="formula-edit">Edit Formula</option>
                                <option value="formula-print">Cetak Formula</option>
                                <option value="formula-view">Lihat Formula</option>
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="doctype" class="form-label">Departement</label>
                            <select name="doctype" id="doctype" class="form-select @error('doctype') is-invalid @enderror"
                                required>
                                <option value="">Pilih Departement</option>
                                @foreach ($doctype as $item)
                                    <option {{ old('doctype') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                        {{ $item->doctype . ' - ' . $item->description }}</option>
                                @endforeach
                            </select>
                            @error('doctype')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="user" class="form-label">Pilih User</label>
                            <select name="user" id="user" class="form-select @error('user') is-invalid @enderror"
                                required>
                                <option value="">Pilih user</option>
                                @foreach ($users as $item)
                                    <option {{ old('user') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">
                                        {{ $item->name . ' - ' . $item->email }}</option>
                                @endforeach
                            </select>
                            @error('user')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="level" class="form-label">level</label>
                            <input type="number" class="form-control bg-white @error('level') is-invalid @enderror"
                                id="level" name="level" value="{{ old('level') ? old('level') : '' }}"
                                placeholder="Level " min="1" max="9" required />

                            @error('level')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-primary" type="submit">Buat Approval</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
        </div>
    </div>
@endsection
