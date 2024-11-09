@extends('layouts.template')

@section('title', 'Edit Produk CI')

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
            <form action="{{ route('ci.update',['id'=>$product->id]) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row">
                    
                    <div class="col-md-6 mb-3">
                        <label for="product_type" class="form-label">Kategori</label>
                        <select name="product_type" id="product_type" class="form-select @error('product_type') is-invalid @enderror" required>
                            @foreach ($categories as $item)
                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                            @endforeach
                        </select>
                        @error('product_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="sap_type" class="form-label">Type</label>
                        <select name="sap_type" id="sap_type" class="form-select  @error('sap_type') is-invalid @enderror" required>
                            <option selected value="HALB">Semi Finish</option>
                            {{-- <option value="ROH1">Raw Material</option>
                            <option value="FERT">Finish Goods</option> --}}
                        </select>
                        @error('sap_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="unit" class="form-label">Satuan</label>
                        <input type="text" class="form-control  @error('unit') is-invalid @enderror" id="unit"
                            name="unit" value="{{ old('unit') ? old('unit') : $product->uom }}" placeholder="UNIT " required />
                        @error('unit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="product_code" class="form-label">Kode Produk CI</label>
                        <input type="text" class="form-control  @error('product_code') is-invalid @enderror" id="product_code"
                            name="product_code" value="{{ old('product_code') ? old('product_code') : $product->product_code }}" placeholder="Kode Produk " />
                        @error('product_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="desc" class="form-label">Nama Material</label>
                        <input type="text" class="form-control  @error('desc') is-invalid @enderror" id="desc"
                            name="desc" value="{{ old('desc') ? old('desc') : $product->description}}" placeholder="Nama Material "
                            required />
                        @error('desc')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    

                    <div class="col-md-12 mb-3">
                        <label for="sap_code" class="form-label">Kode SAP (isi jika sudah ada kode SAP)</label>
                        <input type="text" class="form-control  @error('sap_code') is-invalid @enderror" id="sap_code"
                            name="sap_code" value="{{ old('sap_code') ? old('sap_code') : $product->sap_code }}" placeholder="Kode SAP " />
                        @error('sap_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-outline-primary" name="action" value="active" type="submit">{{ $product->is_active?'Nonaktifkan':'Aktifkan' }}</button>
                    <button class="btn btn-primary" name="action" value="update" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>


@endsection
