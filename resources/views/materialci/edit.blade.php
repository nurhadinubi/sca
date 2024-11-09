@extends('layouts.template')

@section('title', 'Create Material CI')

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
            <form action="{{ route('ci.store') }}" method="post">
							@csrf
                <div class="col-12 mb-3">
                    <label for="desc" class="form-label">Nama Material</label>
                    <input type="text" class="form-control  @error('desc') is-invalid @enderror" id="desc"
                        name="desc" value="{{ old('desc') ? old('desc') : '' }}" placeholder="Nama Material "
                       required />
                    @error('desc')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="unit" class="form-label">Unit</label>
                    <input type="text" class="form-control  @error('unit') is-invalid @enderror" id="unit"
                        name="unit" value="{{ old('unit') ? old('unit') : 'KG' }}" placeholder="UNIT "
                        required />
                    @error('unit')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12 mb-3">
                    <label for="sap_code" class="form-label">Kode SAP (isi jika sudah ada kode SAP)</label>
                    <input type="text" class="form-control  @error('sap_code') is-invalid @enderror" id="sap_code"
                        name="sap_code" value="{{ old('sap_code') ? old('sap_code') : '' }}" placeholder="Kode SAP "
                         />
                    @error('sap_code')
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
    </div>


@endsection
