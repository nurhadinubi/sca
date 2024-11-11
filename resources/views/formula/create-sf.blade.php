@extends('layouts.template')

@section('content')
    <div class="">
        @if (session('message'))
            @include('components.flash', [
                'type' => session('message')['type'],
                'text' => session('message')['text'],
            ])
        @endif
        {{-- @if ($errors->any())
            {!! implode('', $errors->all('<div>:message</div>')) !!}
        @endif --}}
        <div class="card">
            <div class="card-body">
                  <form action="{{ route('sf.storeWithKeycode',['id'=>$keycode->key_code]) }}" method="post" id="form-scaleup"
                    data-listScaleUp="{{ route('scaleup.listScaleUp') }}">
                    @csrf

                    <div class="col mb-3">
                        <label for="scaleup" class="form-label">Pilih No Scale Up</label>
                        <select name="scaleup" id="scaleup" class="form-select ">
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="plant" class="form-label required">Plant</label>
                            <input type="text" class="form-control  @error('plant') is-invalid @enderror" id="plant"
                                name="plant" value="{{ old('plant') ? old('plant') : '' }}" placeholder="Kode Plant"
                                required>

                            @error('plant')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="bom_usage" class="form-label required">BOM Usage</label>
                            <input type="text" class="form-control  @error('bom_usage') is-invalid @enderror"
                                id="bom_usage" name="bom_usage" value="{{ old('bom_usage') ? old('bom_usage') : '1' }}"
                                placeholder="1" required>

                            @error('bom_usage')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="doc_date" class="form-label required">Tanggal Dokumen</label>
                            <input type="text" class="form-control form-read-only  @error('doc_date') is-invalid @enderror"
                                id="doc_date" name="doc_date" value="{{ old('doc_date') ? old('doc_date') : \Carbon\carbon::now()->format('d-m-Y') }}"
                                readonly required>

                            @error('doc_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        

                        <div class="col-md-2 mb-3">
                            <label for="material" class="form-label required">Material</label>
                            <input type="text" class="form-control  @error('material') is-invalid @enderror"
                                id="material" name="material" value="{{ old('material') ? old('material') : '' }}"
                                placeholder="Material">
                            @error('material')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="material_description" class="form-label required">Deskripsi</label>
                            <input type="text" class="form-control  @error('material_description') is-invalid @enderror"
                                id="material_description" name="material_description" value="{{ old('material_description') ? old('material_description') : '' }}"
                                placeholder="material_description" required>
                            @error('material_description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="col-md-2 mb-3">
                            <label for="valid_from" class="form-label required">Valid From</label>
                            <input type="text" class="form-control  @error('valid_from') is-invalid @enderror"
                                id="valid_from" name="valid_from" value="{{ old('valid_from') ? old('valid_from') : '' }}"
                                placeholder="Pilih tanggal" required>

                            @error('valid_from')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="total" class="form-label required">Total Qty Scale Up</label>
                            <input type="number" min="1" class="form-control form-read-only  @error('total') is-invalid @enderror"
                                id="total" name="total" value="{{ old('total') ? old('total') : '' }}"
                                placeholder="Total Qty" required>

                            @error('total')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="base_qty" class="form-label required">Base Qty</label>
                            <input type="number" min="1"
                                class="form-control  @error('base_qty') is-invalid @enderror" id="base_qty" name="base_qty"
                                value="{{ old('base_qty') ? old('base_qty') : '' }}" placeholder="base Qty" required>
                            @error('base_qty')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="header_remark" class="form-label">Text</label>
                            <input type="text" class="form-control  @error('header_remark') is-invalid @enderror"
                                id="header_remark" name="header_remark"
                                value="{{ old('header_remark') ? old('header_remark') : '' }}"
                                placeholder="header_remark" required>

                            @error('header_remark')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        {{-- <button type="button" class="btn btn-outline-primary mb-3" id="add-row">Tambah item</button> --}}

                        <table class="table table-bordered table-sm table-small-font">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-2">Material Code</th>
                                    <th scope="col" class="col-4">Material Description</th>
                                    <th scope="col" class="col-1">Percent</th>
                                    <th scope="col" class="col-1">Qty Scaleup</th>
                                    <th scope="col" class="col-1">Qty SAP</th>
                                    <th scope="col" class="col-1">Sloc</th>
                                    <th scope="col" class="col-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detailTable">
                                @foreach (old('detail', $data['detail'] ?? []) as $index => $detail)
                                    <tr data-index="{{ $index }}">
                                        <td><input class="form-control form-control-sm" type="text"
                                                name="detail[{{ $index }}][material_code]"
                                                value="{{ old('detail.' . $index . '.material_code', $detail['material_code'] ?? '') }}"
                                                ></td>
                                        <td><input class="form-control form-control-sm form-read-only" type="text"
                                                name="detail[{{ $index }}][material_description]"
                                                value="{{ old('detail.' . $index . '.material_description', $detail['material_description'] ?? '') }}"
                                                readonly></td>
                                        <td><input class="form-control form-control-sm form-read-only" type="text"
                                                name="detail[{{ $index }}][percent]"
                                                value="{{ old('detail.' . $index . '.percent', $detail['percent'] ?? '') }}"
                                                readonly></td>
                                        <td><input class='form-control form-control-sm form-read-only' type="text"
                                                name="detail[{{ $index }}][qty]"
                                                value="{{ old('detail.' . $index . '.qty', $detail['qty'] ?? '') }}"></td>
                                        <td><input
                                                class="form-control form-control-sm  @error('detail.' . $index . '.qty_sap') is-invalid @enderror"
                                                type="text" name="detail[{{ $index }}][qty_sap]"
                                                value="{{ old('detail.' . $index . '.qty_sap', '') }}"
                                                placeholder="Qty SAP">

                                        </td>
                                        <td><input
                                                class="form-control form-control-sm  @error('detail.' . $index . '.sloc') is-invalid @enderror"
                                                type="text" name="detail[{{ $index }}][sloc]"
                                                value="{{ old('detail.' . $index . '.sloc', $detail['sloc'] ?? '') }}">
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn btn-danger btn-sm remove-row">Delete</button>
                                        </td>
                                        <input type="hidden" name="detail[{{ $index }}][id]"
                                            value="{{ old('detail.' . $index . '.id', $detail['id'] ?? '') }}">

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="/" class="btn btn-outline-primary">Batal</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

    @push('custom-script')
        <script>
            const validDate = flatpickr("#valid_from", {
                dateFormat: "d-m-Y",
                altFormat: "d-m-Y",
                defaultDate: "today",
            });

            $(document).ready(function() {

                var rowIndex = $('tbody#detailTable tr').length;

                $('#add-row').click(function() {
                    var newRow = renderRow(rowIndex, {
                        material_code: '',
                        material_description: '',
                        percent: '',
                        qty: 0,
                        sloc: '',
                        uom: '',
                        id: ''
                    });
                    $('#detailTable').append(newRow);
                    rowIndex++;
                });

                // Remove row
                $(document).on('click', '.remove-row', function() {
                    $(this).closest('tr').remove();
                });

                function renderRow(index, detail, total = 0) {
                    return `
                        <tr data-index="${index}">
                            <td scope="col" class="col-2" ><input class="form-control form-control-sm" type="text" name="detail[${index}][material_code]" value="${detail.material_code}"></td>
                            <td scope="col" class="col-4"><input class="form-control form-read-only form-control-sm" type="text" name="detail[${index}][material_description]" value="${detail.material_description}" readonly></td>
                            <td scope="col" class="col-1"><input class="form-control form-read-only form-control-sm" type="text" name="detail[${index}][percent]" value="${detail.percent+'%'}" readonly></td>
                            <td scope="col" class="col-1"><input class="form-control form-read-only form-control-sm" type="text" name="detail[${index}][qty]" value="${parseFloat(detail.percent*total/100).toFixed(3)+detail.uom}" readonly></td>
                            <td scope="col" class="col-1"><input class="form-control form-control-sm" type="text" name="detail[${index}][qty_sap]" value="" placeholder="Qty SAP" required></td>
                            <td scope="col" class="col-1"><input class="form-control form-control-sm" type="text" name="detail[${index}][sloc]" value="${detail.sloc?detail.sloc:''}" required placeholder="Sloc"></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row">Delete</button>
                            </td>
                            <input type="hidden" name="detail[${index}][id]" value="${detail.id}">
                        </tr>
                     `;
                }

                var token = $('meta[name="csrf-token"]').attr("content");
                $("#scaleup").select2({
                    placeholder: "Pilih Nomor Scale UP",
                    theme: "bootstrap-5",
                    ajax: {
                        url: $('#form-scaleup').attr("data-listScaleUp"),
                        type: "get",
                        dataType: "json",
                        casesensitive: false,
                        processResults: (data) => {
                           
                            return {
                                results: data.map((res) => {
                                    return {
                                        text: `${(res.product_code?res.product_code:'')  + ' - ' + (res.material_description?res.material_description:'')  + " - " + (res.revision?res.revision:'')+" - "+res.doc_number}`,
                                        id: res.id,
                                    };
                                }),
                            };
                        },
                    },
                });

                var old = @json(old('scaleup'));
            
                if (old) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('scaleup.getByID') }}",
                        data: {
                            _token: token,
                            id: old,
                        },
                        dataType: "JSON",
                        success: function(response) {
                            let option = new Option(
                                response.header.product_code +
                                " - " +
                                response.header.material_code +
                                " - " +
                                response.header.material_description +
                                " - " +
                                response.header.doc_number,
                                old,
                                true,
                                true
                            );
                            $('#scaleup').append(option).trigger("change");
                        },
                        error: function(request, status, error) {
                            console.log(error);
                        },
                    });
                }


                $('#scaleup').on("select2:select", function() {
                    $.ajax({
                        url: "{{ route('scaleup.getByID') }}",
                        type: "POST",
                        data: {
                            _token: token,
                            id: $(this).val(),
                        },
                        dataType: "json",
                        success: function(response) {
                            var header = response.header
                            if (!old) {
                                $('#material').val(header.material_code)
                                $('#material_description').val(header.material_description)
                            const formattedDate = header.issue_date.split('-').reverse().join('-');

                            validDate.setDate(formattedDate);
                            $('#total').val(header.total)

                            const oldQty =
                                @json(old('qty', [])); // Mengambil nilai qty lama dari request jika ada kesalahan validasi
                            const tableBody = document.getElementById('detailTable');
                            const data = response.detail

                            tableBody.innerHTML = ""

                            data.forEach((item, index) => {
                                var row = renderRow((rowIndex + index), item, header.total);
                                $('#detailTable').append(row);
                                // rowIndex++;
                            });
                            }

                        },
                        error: function(e) {
                            console.log(e)
                        }

                    })
                })

            })
        </script>
    @endpush
@endsection
