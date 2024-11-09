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
            <form id="product-code" action="{{ route('pc.store') }}" method="post"
                data-getMaterial = "{{ route('productList', ['type' => 'HALB']) }}">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="sub_category" class="form-label  @error('sub_category') is-invalid @enderror">Kategori</label>
                        <select name="sub_category" id="sub_category" class="form-select" required>
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

                    <div class="col-md-4 mb-3">
                        <label for="code" class="form-label">Kode Produk</label>
                        <input type="text" class="form-control  @error('code') is-invalid @enderror" id="code"
                            name="code" value="{{ old('code') ? old('code') : '' }}" placeholder="Kode Produk "
                            required />
                        @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sap_type" class="form-label">Type</label>
                        <select name="sap_type" id="sap_type" class="form-select  @error('sap_type') is-invalid @enderror" readonly>
                            <option selected value="HALB">Semi Finish</option>
                        </select>
                        @error('sap_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="material" class="form-label">Kode SAP</label>
                        <select id="material" class="form-control  @error('material') is-invalid @enderror" name="material" required>
                            <option value="" selected disabled>Pilih Material</option>   
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
        {{-- <script src="{{ asset('assets/custom/js/productcode/create.js') }}"></script> --}}
        <script>
            $(document).ready(function() {
                const path = $("#product-code").attr("data-getmaterial");

                $("#material").select2({
                    placeholder: "Pilih material atau cari",
                    theme: "bootstrap-5",
                    ajax: {
                        url: path,
                        type: "GET",

                        dataType: "json",
                        casesensitive: false,
                        processResults: (data) => {
                            return {
                                results: data.map((res) => {
                                    return {
                                        text: ` ${res.id} - ${
                                     res.sap_code
                                    ? res.sap_code
                                    : "No SAP Code" } - ${res.description} ${res.product_code?"("+ res.product_code + ")":''}`,
                                        id: res.id,
                                    };
                                }),
                            };
                        },


                    },
                    language: {
                        noResults: function() {
                            return `<a href="{{ route('ci.create') }}" type="button"
                                    class="btn btn-primary w-100" >+ Tambah Material</a>
                                    `;
                        },
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
            });
        </script>
    @endpush

@endsection
