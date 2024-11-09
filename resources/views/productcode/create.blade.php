@extends('layouts.template')

@section('title', 'Create Product Code')

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
            <form id="product-code"  action="{{ route('pc.store') }}" method="post"
            data-getMaterial = "{{ route('api.getMaterial') }}"
            >
                @csrf

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="sub_category" class="form-label">Sub Division</label>
                    <select name="sub_category" id="sub_category" class="form-select">
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}">{{ $item->code . ' - ' . $item->description }}</option>
                        @endforeach
                    </select>
                    @error('sub_category')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Kode Produk</label>
                    <input type="text" class="form-control  @error('code') is-invalid @enderror" id="code"
                        name="code" value="{{ old('code') ? old('code') : '' }}" placeholder="Kode Produk " required />
                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="description" class="form-label">Keterangan</label>
                    <input type="text" class="form-control  @error('description') is-invalid @enderror" id="description"
                        name="description" value="{{ old('description') ? old('description') : '' }}"
                        placeholder="Keterangan " required />
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-12 mb-3">
                    <label for="material" class="form-label">Material</label>
                    <select id="material" class="form-control" name="material">
                        <option value="" selected disabled>Pilih Material</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                    @error('material')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>


    @push('custom-script')
        <script src="{{ asset('assets/custom/js/productcode/create.js') }}">
        </script>
    @endpush

@endsection
