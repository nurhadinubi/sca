@extends('layouts.template')

@section('content')
<div class="">
    @if (session('message'))
    @include('components.flash', [
    'type' => session('message')['type'],
    'text' => session('message')['text'],
    ])
    @endif

    <div class="card">
        <div class="card-body">
            <form action="" method="post" id="form-scaleup" data-listScaleUp="{{ route('scaleup.listScaleUp') }}">
                @csrf

                <div class="col mb-3">
                    <label for="scaleup" class="form-label">Pilih No Scale Up</label>
                    <select name="scaleup" id="scaleup" class="form-select ">
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="plant" class="form-label required">Plant</label>
                        <input type="text" class="form-control bg-white @error('plant') is-invalid @enderror"
                            id="plant" name="plant" value="{{ old('plant') ? old('plant') : '' }}"
                            placeholder="Kode Plant" required>

                        @error('plant')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="bom_usage" class="form-label required">BOM Usage</label>
                        <input type="text" class="form-control bg-white @error('bom_usage') is-invalid @enderror"
                            id="bom_usage" name="bom_usage" value="{{ old('bom_usage') ? old('bom_usage') : '1' }}"
                            placeholder="1" required>

                        @error('bom_usage')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="valid_from" class="form-label required">Valid From</label>
                        <input type="text" class="form-control bg-white @error('valid_from') is-invalid @enderror"
                            id="valid_from" name="valid_from" value="{{ old('valid_from') ? old('valid_from') : '' }}"
                            placeholder="Pilih tanggal" required>

                        @error('valid_from')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="material" class="form-label required">Material</label>
                        <input type="text" class="form-control bg-white @error('material') is-invalid @enderror"
                            id="material" name="material" value="{{ old('material') ? old('material') : '' }}" 
                            placeholder="Material" required>

                        @error('material')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="base_qty" class="form-label required">Base Qty</label>
                        <input type="number" min="1" class="form-control bg-white @error('base_qty') is-invalid @enderror"
                            id="base_qty" name="base_qty" value="{{ old('base_qty') ? old('base_qty') : '' }}"
                            placeholder="base Qty" required>

                        @error('base_qty')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="header_remark" class="form-label">Text</label>
                        <input type="text" class="form-control bg-white @error('header_remark') is-invalid @enderror"
                            id="header_remark" name="header_remark" value="{{ old('header_remark') ? old('header_remark') : '' }}" readonly
                            placeholder="header_remark" required>

                        @error('header_remark')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div>
                    <table class="table table-bordered table-sm table-small-font">
                        <thead>
                            <tr>
                                <th>Material Code</th>
                                <th>Material Description</th>
                                <th>Percent</th>
                                <th>Qty Scaleup</th>
                                <th>Qty SAP</th>
                                <th>SLOC</th>
                            </tr>
                        </thead>
                        <tbody id="detailTable">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
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
                    console.log(data);
                    return {
                        results: data.map((res) => {
                            return {
                                text: `${(res.product_code?res.product_code:'')  + ' - ' + (res.material_desc?res.material_desc:'')  + " - " + (res.revision?res.revision:'')+" - "+res.doc_number}`,
                                id: res.id,
                            };
                        }),
                    };
                },
            },

        });

        $('#scaleup').on("select2:select", function() {
            $.ajax({
                url: "/scaleup/getByID",
                type: "POST",
                data: {
                    _token: token,
                    id: $(this).val(),
                },
                dataType: "json",
                success: function(response) {
                    var header = response.header
                    console.log(response)
                    $('#material').val(header.material_code + '-' + header
                        .material_description)
                    const formattedDate = header.issue_date.split('-').reverse().join('-');

                    validDate.setDate(formattedDate);


                    const oldQty = @json(old('qty', [])); // Mengambil nilai qty lama dari request jika ada kesalahan validasi
                    const tableBody = document.getElementById('detailTable');
                    const data = response.detail

                    tableBody.innerHTML = ""
                    data.forEach((item, index) => {
                        const qtyValue = oldQty[index] !== undefined ? oldQty[
                            index] : (item.qty !== null ? item.qty : '');
                        const row = document.createElement('tr');

                        row.innerHTML = `
                                    <td>${item.material_code}</td>
                                    <td>${item.material_description}</td>
                                    <td>${item.percent}%</td>
                                    <td>${parseFloat(item.percent*header.total/100).toFixed(2)+item.uom}</td>
                                    
                                    <td>
                                        <input type="text" name="qty[]" value="${qtyValue}" class="form-control">
                                    </td>
                                     <td>
                                        <input type="text" name="sloc[]" value="" class="form-control">
                                    </td>
                                `;

                        tableBody.appendChild(row);
                    });



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