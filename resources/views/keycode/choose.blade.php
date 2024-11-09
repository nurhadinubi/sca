@extends('layouts.template')

@section('title', 'Pilih transaksi')


@section('content')
    <div class="row">
        <div class="col">

            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
            {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('keycode.request') }}" method="post" id="choose-menu"
                        data-listScaleUp="{{ route('scaleup.listScaleUp') }}"
                        data-productExist="{{ route('ci.productExist') }}" data-productById="{{ route('ci.productById') }}"
                        data-getHeader="{{ route('scaleup.getHeader') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-3">
                                <input type="radio" id="scaleup-create" name="menu" value="scaleup-create">
                                <label for="scaleup-create">Tambah Scaleup</label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" id="scaleup-edit" name="menu" value="scaleup-edit">
                                <label for="scaleup-edit">Edit Scaleup</label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" id="scaleup-view" name="menu" value="scaleup-view">
                                <label for="scaleup-view">Display Scaleup</label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" id="scaleup-print" name="menu" value="scaleup-print">
                                <label for="scaleup-view">Print Scaleup</label>
                            </div>
                        </div>

                        <div class="mt-4 scaleup-options">
                            <div class="row">
                                <div class="col-md-6 mb-3 scaleup-select">
                                    <label for="scaleup" class="form-label">Cari Nama atau Kode Produk</label>
                                    <select name="scaleup" id="scaleup" class="form-select ">
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="product_code" class="form-label required">Kode Produk</label>
                                    <select type="text" class="form-control  @error('product_code') is-invalid @enderror"
                                        id="product_code" name="product_code"
                                        value="{{ old('product_code') ? old('product_code') : '' }}" readonly
                                        placeholder="product_code" required></select>
                                    @error('product_code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label for="sap_code" class="form-label required">Kode SAP</label>
                                    <input type="text" class="form-control  @error('sap_code') is-invalid @enderror"
                                        id="sap_code" name="sap_code" value="{{ old('sap_code') ? old('sap_code') : '' }}"
                                        readonly placeholder="sap_code" required>
                                    @error('sap_code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="remark" class="form-label required">Keterangan</label>
                                    <textarea type="text" class="form-control  @error('remark') is-invalid @enderror" id="remark" name="remark"
                                        placeholder="Keteranagn" required>{{ old('remark') ? old('remark') : '' }} </textarea>
                                    @error('remark')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="/" class="btn btn-outline-primary" type="submit">Batal</a>
                                <button class="btn btn-primary" type="submit">Request</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


    @push('custom-script')
        <script>
            $(document).ready(function() {
                const prodCode = $("#product_code");
                $('#scaleup').closest('.scaleup-select').hide();
                var token = $('meta[name="csrf-token"]').attr("content");

                function toggleProdCodeAccess(allowOpen) {
                    if (allowOpen) {
                        prodCode.off('select2:opening select2:closing'); // Hapus pencegahan
                    } else {
                        prodCode.on('select2:opening select2:closing', function(e) {
                            e.preventDefault(); // Mencegah pembukaan atau penutupan dropdown
                        });
                    }
                }
                $('input[type=radio][name=menu]').change(function() {
                    prodCode.val('').trigger('change.select2')
                    prodCode.val('').trigger('change')
                    $("#scaleup").val('').trigger('change.select2')
                    prodCode.select2({
                        placeholder: "Pilih Kode Products",
                        // dropdownParent: parent,
                        theme: "bootstrap-5",
                        ajax: {
                            url: $("#choose-menu").attr("data-productExist"),
                            type: "GET",
                            dataType: "json",
                            casesensitive: false,
                            processResults: (data) => {
                                return {
                                    results: data.map((res) => {
                                        return {
                                            text: `${res.product_code} - ${res.description}`,
                                            id: res.id,
                                        };
                                    }),
                                };
                            },
                        },

                        language: {
                            noResults: function() {
                                return `<a href="{{ route('ci.create') }}" type="button"
                                    class="btn btn-primary w-100">+ Tambah Kode Produk</a>
                                    `;
                            },
                        },

                        escapeMarkup: function(markup) {
                            return markup;
                        },
                    });

                    prodCode.on("select2:select", function() {
                        console.log($(this).val());
                        const route = $("#choose-menu").attr("data-productById");

                        $.ajax({
                            type: "GET",
                            url: route,
                            data: {
                                id: $(this).val(),
                            },
                            dataType: "JSON",
                            success: function(response) {
                                $('#sap_code').val(response.sap_code)
                                // $('#material_description').val(response.description)
                                // $('#base_uom').val(response.uom)
                                // $('#product_code').val(response.product_code)

                            },
                            error: function(request, status, error) {
                                console.log(error);
                            },
                        });
                    });

                    if (this.id === 'scaleup-create') {
                        $('#scaleup').closest('.scaleup-select')
                    .hide(); // Menyembunyikan #scaleup dan div di atasnya
                        // Product Kode
                        toggleProdCodeAccess(true)
                    } else {
                        toggleProdCodeAccess(false)
                        $('#scaleup').closest('.scaleup-select').show();
                        $("#scaleup").select2({
                            placeholder: "Pilih Nomor Scale UP",
                            theme: "bootstrap-5",
                            ajax: {
                                url: $('#choose-menu').attr("data-listScaleUp"),
                                type: "get",
                                dataType: "json",
                                casesensitive: false,
                                processResults: (data) => {
                                    return {
                                        results: data.map((res) => {
                                            return {
                                                text: `${(res.product_code?res.product_code:'')  + ' - ' + (res.material_description?res.material_description:'')  + " - Revisi:" + (res.revision?res.revision:'')+""+" - " +(res.status=='A'?'Approve':res.status=='P'?'Pending':'Reject')}`,
                                                id: res.id,
                                            };
                                        }),
                                    };
                                },
                            },
                        });
                        $("#scaleup").on("select2:select", function() {
                            $.ajax({
                                url: $('#choose-menu').attr("data-getHeader"),
                                type: "POST",
                                data: {
                                    _token: token,
                                    id: $(this).val(),
                                },
                                dataType: "json",
                                success: function(response) {
                                    var newOption = new Option(response.product_code +
                                        " - " + response.material_description, response
                                        .material_id, true, true);
                                    prodCode.append(newOption).trigger('change');
                                    $('#sap_code').val(response.material_code)
                                    prodCode.on('select2:opening select2:closing',
                                        function(e) {
                                            e
                                        .preventDefault(); // Mencegah pembukaan atau penutupan dropdown
                                        });
                                },

                                error: function(error) {
                                    console.log(error)
                                },

                            })
                        })

                    }
                });
            });
        </script>
    @endpush

@endsection
