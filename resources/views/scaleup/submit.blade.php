@extends('layouts.template')

@section('title', 'Submit Scaleup untuk ke fromula')

@section('content')
    <div class="row">
        <form action="" method="post" {{ route('scaleup.submitStore') }}>
          @csrf
            <div class="card">
                <div class="card-body">
                    <div class="col mb-3">
                        <label for="scaleup_id" class="form-label required">Kode Produk</label>
                        <select type="text" class="form-control  @error('scaleup_id') is-invalid @enderror"
                            id="scaleup_id" name="scaleup_id"
                            value="{{ old('scaleup_id') ? old('scaleup_id') : '' }}" readonly placeholder="scaleup_id"
                            required></select>
                        @error('scaleup_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <div id="head-scaleup">

                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                      <a href="{{ route('index') }}" class="btn btn-outline-info">Kembali</a>
                      <button type="submit" class="btn btn-outline-primary hidden disabled" >Submit</button>
                  </div>
                    
                </div>
            </div>
        </form>
    </div>
@endsection

@push('custom-script')
    <script>
        const prodCode = $("#scaleup_id");
        const token = $('meta[name="csrf-token"]').attr("content");

        function renderHeader(header, element) {
            var headerEl = "";
            headerEl += `
                        <div class="row">
                          <span class="col-2">No Doc</span>
                          <span class="col-10">: ${header.doc_number}</span>
                        </div>
                        <div class="row">
                          <span class="col-2">Doc. Date</span>
                          <span class="col-10">: ${header.doc_date}</span>
                        </div>
                        <div class="row">
                          <span class="col-2">Tanggal terbit</span>
                          <span class="col-10">: ${header.issue_date}</span>
                        </div>
                        <div class="row">
                          <span class="col-2">Material</span>
                          <span class="col-10">: ${header.product_code + " - " + header.material_description  }</span>
                        </div>
                      
                        <div class="row">
                          <span class="col-2">Total</span>
                          <span class="col-10">: ${header.total } - ${header.base_uom}</span>
                        </div>

                      `
            $(element).html("");
            $(element).html(headerEl);
            $('button').removeClass('disabled')
        }

        prodCode.select2({
            placeholder: "Pilih Kode Products",
            // dropdownParent: parent,
            theme: "bootstrap-5",
            ajax: {
                url: "{{ route('scaleup.listToSubmit') }}",
                type: "GET",
                dataType: "json",
                casesensitive: false,
                processResults: (data) => {
                    return {
                        results: data.map((res) => {
                            return {
                                text: `${res.product_code} - ${res.material_description} || Revisi : ${res.revision}`,
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
            $.ajax({
                url: "{{ route('scaleup.getHeader') }}",
                type: "POST",
                data: {
                    _token: token,
                    id: $(this).val(),
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    renderHeader(response, '#head-scaleup')



                },
                error: function(e) {
                    console.log(e)
                }

            })
        })
    </script>
@endpush
