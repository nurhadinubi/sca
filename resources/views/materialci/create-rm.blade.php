@extends('layouts.template')

@section('title', 'Create Raw material')

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
        </div>

        <div class="card-body">
            <form action="{{ route('sap.store') }}" method="post">
                @csrf
                <div class="row">                   
                    <div class="col-md-6 mb-3">
                        <label for="sap_type" class="form-label">Type</label>
                        <select name="sap_type" id="sap_type" class="form-select  @error('sap_type') is-invalid @enderror" required>
                            <option selected value="ROH1">Raw Material</option>
                        </select>
                        @error('sap_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sap_code" class="form-label">Kode SAP (Jika sudah ada)</label>
                        <input type="text" class="form-control  @error('sap_code') is-invalid @enderror" id="sap_code"
                            name="sap_code" value="{{ old('sap_code') ? old('sap_code') : '' }}" placeholder="Kode Produk " />
                        @error('sap_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="desc" class="form-label required">Nama Material</label>
                        <input type="text" class="form-control  @error('desc') is-invalid @enderror" id="desc"
                            name="desc" value="{{ old('desc') ? old('desc') : '' }}" placeholder="Nama Material "
                            required />
                        @error('desc')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="unit" class="form-label required">Satuan</label>
                        <input type="text" class="form-control  @error('unit') is-invalid @enderror" id="unit"
                            name="unit" value="{{ old('unit') ? old('unit') : 'KG' }}" placeholder="UNIT " required />
                        @error('unit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="/" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>


@endsection
