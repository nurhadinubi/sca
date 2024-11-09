@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                    <strong>{{ $errors->all()[0] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form id="form-scaleup" onkeydown="return event.key != 'Enter';" class="row g-3 needs-validation"
                        action="{{ route('scaleup.storeWithKeycode',['id'=>$keycode->key_code]) }}" method="POST"
                        data-getMaterial="{{ route('api.getMaterial') }}"
                        data-getMaterialById="{{ route('api.getMaterialById') }}"
                        data-getProducts = "{{ route('getProducts') }}"
                        data-getItemById = "{{ route('scaleup.getItemById') }}"
                        data-getItemcategory = "{{ route('scaleup.getItemcategory') }}"
                        data-sessionGetSubCategory = "{{ route('scaleup.sessionGetSubCategory') }}"
                        data-updateItemcategory = "{{ route('scaleup.updateItemcategory') }}"
                        data-deleteItemcategory = "{{ route('scaleup.deleteItemcategory') }}"
                        data-deleteItem = "{{ route('scaleup.deleteItem') }}"
                        data-productExist="{{ route('ci.productExist') }}"
                        data-productById="{{ route('ci.productById') }}"
                        data-sapList="{{ route('sap.list',['type'=>'ROH1']) }}"
                        data-sapById="{{ route('sap.byId') }}"
                        >
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex align-items-center my-3">
                                        <i id="toggleIcon" class="fas fa-caret-down me-2" role="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-expanded="false" aria-controls="collapseExample"></i>
                                        <h5 class="mb-0">Header</h5>
                                    </div>
                                    <div class="collapse show" id="collapseExample">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="IssueDate" class="form-label required">Tanggal terbit</label>
                                                <input type="text"
                                                    class="form-control  @error('IssueDate') is-invalid @enderror"
                                                    id="IssueDate" name="IssueDate"
                                                    value="{{ old('IssueDate') ? old('IssueDate') : '' }}" readonly
                                                    placeholder="Pilih tanggal" required>

                                                @error('IssueDate')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="DocDate" class="form-label required ">Document Date</label>
                                                <input type="text" class="form-control form-read-only" id="DocDate" name="DocDate"
                                                    value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required readonly>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="product_select" class="form-label required">Kode Produk CI</label>
                                                <select name="product_select" id="product_select"
                                                    class="form-select  @error('product_select') is-invalid @enderror"
                                                    required>

                                                    <option value="{{ $keycode->product_id }}"> {{ $keycode->product_code ." - ". $keycode->product_description  }} </option>
                                                </select>
                                                @error('product_select')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="doctype" class="form-label">Type</label>
                                                <select name="doctype" id="doctype" class="form-select">
                                                    <option value="{{ $keycode->cotegry_id}}"> {{ $keycode->category_description}} </option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="product_code" class="form-label">Kode Produk</label>
                                                <input type="text"
                                                    class="form-control form-read-only  @error('product_code') is-invalid @enderror"
                                                    id="product_code" name="product_code"
                                                    value="{{ old('product_code') ? old('product_code') : $keycode->product_code }}"
                                                    placeholder="Kode Produk CI" readonly />

                                                @error('product_code')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="sap_code" class="form-label">Kode SAP</label>
                                                <input type="text"
                                                    class="form-control form-read-only  @error('sap_code') is-invalid @enderror"
                                                    id="sap_code" name="sap_code"
                                                    value="{{ old('sap_code') ? old('sap_code') : $keycode->sap_code }}"
                                                    placeholder="Kode SAP" readonly />

                                                @error('sap_code')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label for="material_description" class="form-label">Nama Material</label>
                                                <input type="text"
                                                    class="form-control form-read-only  @error('material_description') is-invalid @enderror"
                                                    id="material_description" name="material_description"
                                                    value="{{ old('material_description') ? old('material_description') : $keycode->product_description }}"
                                                    placeholder="Nama Material" readonly/>

                                                @error('material_description')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            
                                            <div class="col-md-3 mb-3">
                                                <label for="revisi" class="form-label">Revisi</label>
                                                <input type="text"
                                                    class="form-control form-read-only  @error('revisi') is-invalid @enderror"
                                                    id="revisi" name="revisi"
                                                    value="{{ old('revisi') ? old('revisi') : '' }}"
                                                    placeholder="Revisi"  readonly/>

                                                @error('revisi')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>


                                            <div class="col-md-3 mb-3">
                                                <label for="total" class="form-label required">Total berat (KG)</label>
                                                <div class="input-group">
                                                    <input type="number" step=".001" min="1"
                                                        class="form-control   @error('total') is-invalid	@enderror"
                                                        id="total" name="total"
                                                        value="{{ session()->has('headerData.total') ? session('headerData.total') : '' }}"
                                                        placeholder="Total berat" required />
                                                    <input class="input-group-text col-sm-3" type="text" value="KG"
                                                        name="base_uom" id="base_uom" readonly/>
                                                    {{-- <button id="btn-base" type="button" class="input-group-text"><i
                                                            class="fa fa-check text-success"></i></button> --}}
                                                    @error('total')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-3 mb-3">
                                                <label for="rev0" class="form-label">Rev00</label>
                                                <input type="text"
                                                    class="form-control @error('rev0') is-invalid	@enderror"
                                                    id="rev0" name="rev0"
                                                    value="{{ old('rev0') ? old('rev0') : '' }}"
                                                    placeholder="Rev00" />
                                                @error('rev0')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label for="per_pack" class="form-label required">Berat Perkemasan</label>
                                                <input type="text"
                                                    class="form-control @error('per_pack') is-invalid	@enderror"
                                                    id="per_pack" name="per_pack"
                                                    value="{{ old('per_pack') ? old('per_pack') : '' }}"
                                                    placeholder="Berat Perkemasan" required />
                                                @error('per_pack')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="remark" class="form-label">Alasan Perubahan</label>
                                                <textarea class="form-control " name="remark" id="remark">{{ old('remark') ? old('remark') : '' }}</textarea>
                                                @error('remark')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr />
                        <div class="col-12 mt-6 border-bottom pb-2">
                            <div class="row ">
                                <div class="col-lg-3">
                                    <h3 class="card-label required">Item List</h3>
                                </div>
                                <div class="col-lg-9 d-flex justify-content-end gap-2">

                                    <button id="btn-sub-item" type="button" class="btn btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#subItemModal" autocomplete="off">
                                        <span class="fas fa-plus-circle"></span>&nbsp; Tambah Kategori
                                    </button>
                                    <button id="btn-add-item" type="button" class="btn btn-primary" autocomplete="off">
                                        <span class="fas fa-plus-circle"></span>&nbsp; Tambah Item
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="load-item">
                           
                            @php
                                $grandTotalQty = 0;
                                $grandTotalPercent = 0;
                            @endphp
                            @if (count($itemCategory))
                                @foreach ($itemCategory as $i)
                                    <div class="mb-3 col-12">
                                        <h5>{{ $i['description'] }} <span data-itemUniq="{{ $i['id'] }}"
                                                title="Edit" class=" flex gap-3"> <i role="button" aria-hidden="true"
                                                    class="fa fa-pencil-alt fa-xs p-2 edit-itemCategory"></i>
                                                <i role="button" aria-hidden="true"
                                                    class="fa fa-trash-alt fa-xs p-2 delete-itemCategory"></i>
                                            </span> </h5>
                                        <table class="table table-striped w-100">
                                            <thead>
                                                <tr>
                                                    <th width="10%">Kode SAP</th>
                                                    <th width="35%">Deskripsi</th>
                                                    <th width="10%">Persentase</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="10%">Remark</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $subtotalQty = 0;
                                                    $subtotalPercent = 0;
                                                @endphp
                                                @if (count($itemCart))
                                                    @foreach ($itemCart as $item)
                                                        @if ($item['item_category'] == $i['id'])
                                                            @php
                                                                $subtotalQty += $item['qty'];
                                                                $subtotalPercent += $item['percent'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $item['item_code'] }}</td>
                                                                <td>{{ $item['item_description'] }}</td>
                                                                <td class="percent">{{ number_format($item['percent'],3) }} %</td>
                                                                <td class="qty">{{ number_format($item['qty'],3) }}</td>
                                                                <td>{{ $item['uom'] }}</td>
                                                                <td>{{ $item['item_remark'] }}</td>
                                                                <td class="d-flex gap-1">
                                                                    <button href="#"
                                                                        class="DeleteButton btn btn-sm outline-none"
                                                                        role="button"
                                                                        data-uniqid="{{ $item['uniqid'] }}"
                                                                        aria-label="hidden"><i
                                                                            class="fa fa-trash text-secondary"></i></button>

                                                                    <button href="#"
                                                                        class="editItem btn btn-sm  outline-none"
                                                                        role="button"
                                                                        data-uniqid="{{ $item['uniqid'] }}"
                                                                        aria-label="hidden"><i
                                                                            class="fa fa-edit text-info"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-center">Subtotal:</th>
                                                    <th class="percent">{{ $subtotalPercent }} %</th>
                                                    <th class="qty">{{ $subtotalQty }}</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    @php
                                        $grandTotalQty += $subtotalQty;
                                        $grandTotalPercent += $subtotalPercent;
                                    @endphp
                                @endforeach

                                <!-- Display Grand Total -->
                                <div class="col-12 mt-4">
                                    <table class="table table-bordered">
                                        <tfoot>
                                            <tr>
                                                <th width="45%" colspan="2" class="text-center">Total Keseluruhan:
                                                </th>
                                                <th width="10%" class="percent">{{ $grandTotalPercent }} %</th>
                                                <th width="10%" class="qty">{{ $grandTotalQty }}</th>
                                                <th width="35%"></th>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif

                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button class="btn btn-outline-info float-end" name="action" value="draft" type="submit">Simpan Draft</button>
                            <button class="btn btn-primary float-end" name="action" value="save" type="submit">Submit Scaleup</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end card -->
        </div> <!-- end col -->

    </div>


    <!-- Static Backdrop Modal -->
    <div class="modal fade modal-lg" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12" id="select-modal">
                            <label for="item" class="form-label block required">Pilih Item</label>
                            <select name="item" id="item" class="form-select w-full block">
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="percent" class="form-label required">Persentase (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control " id="percent" name="percent"
                                    step="0.1" min="0" value="" placeholder="Persentase" required />
                                <span class="input-group-text" id="addon-percent">%</span>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <label for="qty" class="form-label">Qty</label>
                            <input type="number" class="form-control form-read-only" id="qty" name="qty"
                                value="" placeholder="Qty" required readonly />
                        </div>

                        <div class="col-md-4">
                            <label for="uom" class="form-label">Unit</label>
                            <input type="text" class="form-control " id="uom" name="uom"
                                value="KG" placeholder="Uom" required />
                        </div>

                        <div class="col-md-12">
                            <label for="item_remark" class="form-label">Keterangan</label>
                            <input type="text" class="form-control " id="item_remark" name="item_remark"
                                value="" placeholder="Text" />
                        </div>


                        <div class="col-md-12">
                            <label for="item_category" class="form-label required">Item Kategori</label>
                            <select name="item_category" id="item_category" class="form-select">

                                @if (count($itemCategory) >= 1)
                                    @foreach ($itemCategory as $item)
                                        <option value="{{ $item['id'] }}">{{ $item['description'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <input type="text" hidden name="item_code" value="" id="item_code" >
                    <input type="text" hidden name="item_description" value="" id="item_description" >
                    <input type="text" hidden name="item_action" value="add" id="item_action" >
                    <input type="text" hidden name="uniqid" value="" id="uniqid" >

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="add" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Item Category -->
    <div class="modal fade modal-lg" id="subItemModal" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="subItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subItemModalLabel">Tambah Item Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 needs-validation">
                        <div class="col-md-12">
                            <label for="item_category_desc" class="form-label required">Item Kategori</label>
                            <input type="text" class="form-control " id="item_category_desc"
                                name="item_category_desc" value="" placeholder="A. Saus BBQ" />
                        </div>
                    </div>

                    <input type="text" name="id_itemcategory" id="id_itemcategory" value="" readonly hidden>
                    <input type="text" name="id_itemcategory_action" id="id_itemcategory_action" value="add"
                        hidden readonly>

                    @if (count($itemCategory) > 0)
                        <div class="mt-5">
                            <h5>Kategori Item yang sudah dibuat di transaksi ini</h5>
                            <ul class="list-group" id="sessionCategory">
                                @foreach ($itemCategory as $item)
                                    <li class="list-group-item"> {{ $item['description'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="add-category" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    <div id="data-container" data-material="{{ json_encode(old('material_code')) }}"
        data-item="{{ json_encode(old('item')) }}">
    </div>

    @push('custom-script')
        {{-- <script src="{{ asset('assets/custom/js/scaleup/create.js') }}"></script> --}}
        <script>
            flatpickr("#IssueDate", {
                dateFormat: "d-m-Y",
                altFormat: "d-m-Y",
                defaultDate: "today",
            });

            document.addEventListener("DOMContentLoaded", function() {
                var toggleIcon = document.getElementById("toggleIcon");
                var collapseElement = document.getElementById("collapseExample");

                collapseElement.addEventListener("show.bs.collapse", function() {
                    toggleIcon.classList.remove("fa-caret-right");
                    toggleIcon.classList.add("fa-caret-down");
                });

                collapseElement.addEventListener("hide.bs.collapse", function() {
                    toggleIcon.classList.remove("fa-caret-down");
                    toggleIcon.classList.add("fa-caret-right");
                });
            });

            $(document).ready(function() {
                // const dataContainer = document.getElementById("data-container");
                const oldProduct = @json(old('product_select'));
                const oldItem = @json(old('item'));

                var csrftoken = $('meta[name="csrf-token"]').attr("content");

                
                // function edit item

                function editItemEvent(element) {
                    $.ajax({
                        type: "post",
                        url: $("#form-scaleup").attr("data-getItemById"),
                        data: {
                            _token: csrftoken,
                            uniqid: $(element).attr("data-uniqid"),
                        },
                        dataType: "JSON",
                        success: function(response) {
                            $("#add").text("Update");
                            $("#item").val(null).trigger("change");
                            $("#item_category").val(null).trigger("change");

                            var newOption = new Option(
                                response.item_code + " - " + response.item_description,
                                response.material_id,
                                true,
                                true
                            );
                            $("#item").append(newOption).trigger("change");
                            $("#percent").val(response.percent);
                            $("#qty").val(
                                (parseFloat(response.percent) *
                                   Number($("#total").val())) /
                                100
                            );
                            $("#uom").val(response.uom);
                            $("#item_remark").val(response.item_remark);
                            $("#uniqid").val(response.uniqid);

                            $("#item_category option").each(function() {
                                if ($(this).val() == response.item_category) {
                                    $(this).prop("selected", true);
                                } else {
                                    $(this).prop("selected", false);
                                }
                            });
                            $("#item_code").val(response.item_code);
                            $("#item_description").val(response.item_description);

                            $("#item_action").val("edit");
                            $("#staticBackdrop").modal("show");
                        },
                    });
                }

                // Function Delete item
                function deleteItemEvent(element) {
                    if (confirm("apakah anda yakin akan menghapus data ?")) {
                        $.ajax({
                            type: "POST",
                            url: $("#form-scaleup").attr("data-deleteItem"),
                            data: {
                                _token: csrftoken,
                                uniqid: $(element).attr("data-uniqid"),
                            },
                            dataType: "JSON",
                            success: function(response) {
                                var itemCategory = $("#item_category option")
                                    .map(function() {
                                        return {
                                            id: this.value,
                                            text: $(this).text(),
                                        };
                                    })
                                    .get();
                                renderItems(itemCategory, response);
                            },
                            error: function(error) {
                                console.log(error);
                            },
                        });
                    }
                }

                // render table OLD
                function renderItemsOld(itemCategory, itemCart) {
                    const loadItemDiv = $("#load-item");
                    loadItemDiv.empty(); // Kosongkan elemen sebelum render

                    if (itemCategory.length > 0) {
                        itemCategory.forEach((i) => {
                            const categoryDiv = $("<div>").addClass("mb-3 col-12");
                            const categoryHeader = $("<h5>").html(
                                `${
                        i.description ? i.description : i.text
                    } <span data-itemUniq=${
                        i.id
                    } title="Edit" class="flex gap-2"><i role="button" aria-hidden="true" class="fa fa-pencil-alt fa-xs p-2 edit-itemCategory"></i>
                    <i role="button" aria-hidden="true" class="fa fa-trash-alt fa-xs p-2 delete-itemCategory"></i>    
                    </span>`
                            );
                            categoryDiv.append(categoryHeader);

                            const table = $("<table>").addClass(
                                "table table-striped w-100"
                            );
                            const thead = $("<thead>").append(`
                                <tr>
                                    <th width="10%">Kode SAP</th>
                                    <th width="40%">Deskripsi</th>
                                    <th>Persentase</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Action</th>
                                </tr>
                            `);
                            table.append(thead);

                            const tbody = $("<tbody>");

                            const filteredItems = itemCart.filter(
                                (item) => item.item_category === i.id
                            );


                            let totalPercent = 0;
                            let totalQty = 0;
                            if (filteredItems.length > 0) {
                                filteredItems.forEach((item) => {
                                    const row = $("<tr>").append(`
                                        <td>${item.item_code}</td>
                                        <td>${item.item_description}</td>
                                        <td class='percent'>${item.percent} %</td>
                                        <td class='qty'>${item.qty}</td>
                                        <td>${item.uom}</td>
                                        <td class="d-flex gap-1">
                                            <a class="DeleteButton btn btn-sm outline-none"  data-uniqid="${item.uniqid}" aria-label="hidden">
                                                <i class="fa fa-trash text-secondary"></i>
                                            </a>
                                            <a class="editItem btn btn-sm outline-none"  data-uniqid="${item.uniqid}" aria-label="hidden">
                                                <i class="fa fa-edit text-info"></i>
                                            </a>
                                        </td>
                                    `);

                                    totalPercent +=Number(item.percent).toFixed(3);
                                    totalQty +=Number(item.qty).toFixed(3);
                                    tbody.append(row);
                                });
                                const subTotalRow = $("<tr>").append(`                                        
                                        <td colspan='2' class='text-center'>Subtotal</td>
                                        <td class='percent'>${totalPercent}%</td>
                                        <td class='qty'>${totalQty}</td>
                                        <td></td>
                                        <td></td>

                                    `)

                                tbody.append(subTotalRow);
                            }


                            table.append(tbody);
                            categoryDiv.append(table);
                            loadItemDiv.append(categoryDiv);
                        });

                        $(".editItem").click(function(e) {
                            e.preventDefault();
                            editItemEvent(this);
                        });

                        $(".DeleteButton").on("click", function(e) {
                            e.preventDefault();
                            deleteItemEvent(this);
                        });
                    }
                }

                function renderItems(itemCategory, itemCart) {
                    const loadItemDiv = $("#load-item");
                    loadItemDiv.empty(); // Kosongkan elemen sebelum render

                    let grandTotalPercent = 0;
                    let grandTotalQty = 0;

                    console.log(itemCart);
                    if (itemCategory.length > 0) {
                        itemCategory.forEach((i) => {
                            const categoryDiv = $("<div>").addClass("mb-3 col-12");
                            const categoryHeader = $("<h5>").html(
                                `${
                    i.description ? i.description : i.text
                                } <span data-itemUniq=${
                                    i.id
                                } title="Edit" class="flex gap-2"><i role="button" aria-hidden="true" class="fa fa-pencil-alt fa-xs p-2 edit-itemCategory"></i>
                                <i role="button" aria-hidden="true" class="fa fa-trash-alt fa-xs p-2 delete-itemCategory"></i>    
                                </span>`
                            );
                            categoryDiv.append(categoryHeader);

                            const table = $("<table>").addClass("table table-striped w-100");
                            const thead = $("<thead>").append(`
                                <tr>
                                    <th width="10%">Kode SAP</th>
                                    <th width="35%">Deskripsi</th>
                                    <th width="10%">Persentase</th>
                                    <th width="10%">Qty</th>
                                    <th width="10%">Unit</th>
                                    <th width="10%">remark</th>
                                    <th width="15%">Action</th>
                                </tr>
                            `);
                            table.append(thead);

                            const tbody = $("<tbody>");

                            const filteredItems = itemCart.filter(
                                (item) => item.item_category === i.id
                            );

                            let totalPercent = 0;
                            let totalQty = 0;
                            if (filteredItems.length > 0) {
                                filteredItems.forEach((item) => {
                                    const row = $("<tr>").append(`
                                    <td>${item.item_code}</td>
                                    <td>${item.item_description}</td>
                                    <td class='percent'>${item.percent.toFixed(3)} %</td>
                                    <td class='qty'>${item.qty.toFixed(3)}</td>
                                    <td>${item.uom}</td>
                                    <td>${item.item_remark}</td>
                                    <td class="d-flex gap-1">
                                        <a class="DeleteButton btn btn-sm outline-none"  data-uniqid="${item.uniqid}" aria-label="hidden">
                                            <i class="fa fa-trash text-secondary"></i>
                                        </a>
                                        <a class="editItem btn btn-sm outline-none"  data-uniqid="${item.uniqid}" aria-label="hidden">
                                            <i class="fa fa-edit text-info"></i>
                                        </a>
                                    </td>
                                `);

                                    totalPercent +=Number(item.percent);
                                    totalQty +=Number(item.qty);
                                    tbody.append(row);
                                });

                                // Append Subtotal Row
                                const subTotalRow = $("<tr>").append(`
                                    <td colspan='2' width="55%" class='text-center'>Subtotal</td>
                                    <td width="10%" class='percent'>${totalPercent.toFixed(3)}%</td>
                                    <td width="10%" class='qty'>${totalQty.toFixed(3)}</td>
                                    <td width="10%"></td>
                                    <td width="15%"></td>
                                    
                                `);
                                tbody.append(subTotalRow);
                            }

                            grandTotalPercent +=(totalPercent);
                            grandTotalQty += totalQty;

                            table.append(tbody);
                            categoryDiv.append(table);
                            loadItemDiv.append(categoryDiv);
                        });

                        // Append Grand Total
                        const grandTotalDiv = $("<div>").addClass("col-12 mt-4");
                        const grandTotalTable = $("<table>").addClass("table table-bordered");
                        const grandTotalTfoot = $("<tfoot>").append(`
                            <tr>
                                <th colspan="2" width="45%" class="text-center">Total Keseluruhan</th>
                                <th width="10%" class='percent'>${grandTotalPercent.toFixed(3)}%</th>
                                <th width="10%" class='qty'>${grandTotalQty.toFixed(3)}</th>
                                <th width="35%"></th>
                            </tr>
                        `);
                        grandTotalTable.append(grandTotalTfoot);
                        grandTotalDiv.append(grandTotalTable);
                        loadItemDiv.append(grandTotalDiv);

                        $(".editItem").click(function(e) {
                            e.preventDefault();
                            editItemEvent(this);
                        });

                        $(".DeleteButton").on("click", function(e) {
                            e.preventDefault();
                            deleteItemEvent(this);
                        });
                    }
                }


                function renderCategory(itemCategory) {
                    var categoriEl = $("#item_category");
                    categoriEl.html("");
                    var temp = "";
                    itemCategory.forEach(function(i) {
                        temp += `<option value='${i.id}'>${i.description} </option>`;
                    });

                    categoriEl.html(temp);
                }
                // event persentase dirubah
                $("#percent").on("change", function(e) {
                    e.preventDefault();
                    var qty = 0;
                    qty =Number($("#total").val() * (parseFloat(e.target.value) / 100));
                    $("#qty").val(qty);
                });

                function updateQty() {
                    var total =Number($("#total").val());

                    $(".percent").each(function() {
                        var percentage =Number($(this).text().replace("%", ""));

                        var qty = ((percentage / 100) * total).toFixed(3);

                        $(this).closest("tr").find(".qty").text(qty);
                    });
                }
                // Field Total diubah
                $("#total").on("change", function(e) {
                    e.preventDefault();
                    if ($("#total").val() == "" || $("#total").val() <= 0) {
                        alert("Total berat tidak boleh kosong");
                        return
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "/scaleup/total",
                            data: {
                                _token: csrftoken,
                                total: $("#total").val(),
                            },
                            dataType: "JSON",
                            success: function(response) {
                                $("#total").val(parseFloat(response.total));
                                updateQty();
                                // Update qty pada item saat total diubah
                            },
                        });
                    }
                });

                function selctMaterial(el, parent, old = null) {
                    const path = $("#form-scaleup").attr("data-sapList");
                    $(el).select2({
                        placeholder: "Pilih material",
                        dropdownParent: parent,
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
                                            text: `${
                                    res.sap_code ? res.sap_code : ""
                                } - ${res.sap_description}`,
                                            id: res.id,
                                        };
                                    }),
                                };
                            },
                        },

                        language: {
                            noResults: function() {
                                return `<a href="{{ route('sap.createRM') }}" type="button"
                                    class="btn btn-primary w-100" 
                                    onClick='task()'>+ Tambah Material</a>
                                    `;
                            },
                        },

                        escapeMarkup: function(markup) {
                            return markup;
                        },
                    });

                    if (old) {
                        const route = $("#form-scaleup").attr("data-getMaterialById");
                        $.ajax({
                            type: "GET",
                            url: route,
                            data: {
                                id: old,
                            },
                            dataType: "JSON",
                            success: function(response) {
                                let option = new Option(
                                    response.material_code +
                                    " - " +
                                    response.material_description,
                                    old,
                                    true,
                                    true
                                );
                                $(el).append(option).trigger("change");
                            },
                            error: function(request, status, error) {
                                console.log(error);
                            },
                        });
                    }
                }

                selctMaterial("#item", "#select-modal", oldItem);

                $("#item").on("select2:selecting", function() {
                    $("#uom").val("");
                    $("#item_code").val("");
                    $("#item_description").val("");
                });               

                $("#item").on("select2:select", function() {
                    if (
                        $(this).val() != null &&
                        $(this).val() != "" &&
                        typeof $(this).val() != "undefined"
                    ) {
                        const route = $("#form-scaleup").attr("data-sapById");
                        $.ajax({
                            type: "GET",
                            url: route,
                            data: {
                                id: $(this).val(),
                            },
                            dataType: "JSON",
                            success: function(response) {
                            
                                $("#uom").val("KG");
                                $("#item_code").val(response.sap_code);
                                $("#item_description").val(response.sap_description);
                            },
                            error: function(request, status, error) {
                                console.log(error);
                            },
                        });
                    }
                });

                // button Di tambah item di klik
                $("#btn-add-item").on("click", function(e) {
                    $("#add").text("Tambah");
                    $("#uom").val("");
                    $("#item_code").val("");
                    $("#percent").val("");
                    $("#item_description").val("");
                    $("#qty").val("");
                    $("#item_remark").val("");
                    $("#uniqid").val("");
                    $("#item").val("").trigger("change");
                    $("#item_action").val("add");

                    if (
                        $("#total").val() == "" ||
                        $("#total").val() == 0 ||
                        $("#total").val() <= 0
                    ) {
                        alert("Total berat tidak boleh kosong");
                        $('#total').focus()
                        return;
                    }else if(
                         typeof $("#product_select").val() == "undefined" ||
                        $("#product_select").val() == ""||
                        $("#product_select").val() == null         
                    ){
                        alert("Product Kode harus diisi");
                        $('#product_select').focus()
                        return
                    }else {
                        $("#staticBackdrop").modal("show");

                    }
                });

                // Tambah atau edit item
                $("#add").click(function(e) {
                    e.preventDefault();
                    let status = true;
                    if (
                        typeof $("#item").val() == "undefined" ||
                        $("#item").val() == ""||$("#item").val() == null
                    ) {
                        status = false;
                        alert("Material is Required");
                    }

                    if (
                        typeof $("#percent").val() == "undefined" ||
                        $("#percent").val() == ""
                    ) {
                        status = false;
                        alert("Percent is Required");
                    }

                    if (
                        typeof $("#item_category").val() == "undefined" ||
                        $("#item_category").val() == ""|| $("#item_category").val() == null
                    ) {
                        status = false;
                        alert("Item Kategori is Required");
                    }

                    if (status) {
                        $.ajax({
                            type: "POST",
                            url: "/scaleup/item",
                            data: {
                                _token: csrftoken,
                                material_id: $("#item").val(),
                                item_code: $("#item_code").val(),
                                item_description: $("#item_description").val(),
                                qty: $("#qty").val(),
                                percent: $("#percent").val(),
                                uom: $("#uom").val(),
                                item_remark: $("#item_remark").val(),
                                item_category: $("#item_category").val(),
                                action: $("#item_action").val(),
                                item_category_text: $(
                                    "#item_category option:selected"
                                ).text(),
                                uniqid: $("#uniqid").val(),
                            },
                            dataType: "JSON",
                            success: function(response) {
                               console.log(response);
                                var itemCategory = $("#item_category option")
                                    .map(function() {
                                        return {
                                            id: this.value,
                                            text: $(this).text(),
                                        };
                                    })
                                    .get();
                               
                                renderItems(itemCategory, response);
                            },
                            error: function(error) {
                                console.log(error);
                            },
                        });

                        $("#staticBackdrop").modal("hide");
                        $("#uom").val("");
                        $("#item_code").val("");
                        $("#item_description").val("");
                        $("#qty").val("");
                        $("#item_remark").val("");
                        $("#item").val("").trigger("change");
                        $("#add").text("Tambah");
                        $("#item_action").text("add");
                    }
                });

                // Edit Item
                $(".editItem").click(function(e) {
                    e.preventDefault();
                    editItemEvent(this);
                });

                // Delete Item
                $(".DeleteButton").on("click", function(e) {
                    e.preventDefault();
                    deleteItemEvent(this);
                });

                // Product Kode
                // var prodCode = $("#product_select");
                // prodCode.select2({
                //     placeholder: "Pilih Kode Products",
                //     // dropdownParent: parent,
                //     theme: "bootstrap-5",
                //     ajax: {
                //         url: $("#form-scaleup").attr("data-productExist"),
                //         type: "GET",
                //         dataType: "json",
                //         casesensitive: false,
                //         processResults: (data) => {
                //             return {
                //                 results: data.map((res) => {
                //                     return {
                //                         text: `${res.product_code} - ${res.description}`,
                //                         id: res.id,
                //                     };
                //                 }),
                //             };
                //         },
                //     },

                //     language: {
                //         noResults: function() {
                //             return `<a href="{{ route('pc.create') }}" type="button"
                //                     class="btn btn-primary w-100">+ Tambah Kode Produk</a>
                //                     `;
                //         },
                //     },

                //     escapeMarkup: function(markup) {
                //         return markup;
                //     },
                // });

                // if(oldProduct){
                //     const route = $("#form-scaleup").attr("data-productById");
                //     $.ajax({
                //         type: "GET",
                //         url: route,
                //         data: {
                //             id: oldProduct,
                //         },
                //         dataType: "JSON",
                //         success: function(response) { 
                //                let option = new Option(response.product_code +
                //                     " - " +
                //                     response.description,
                //                     oldProduct,
                //                     true,
                //                     true
                //                 );
                //                 $('#product_select').append(option).trigger("change");

                //         },
                //         error: function(request, status, error) {
                //             console.log(error);
                //         },
                //     });
                // }

                // $("#product_select").on("select2:select", function() {
                //     const route = $("#form-scaleup").attr("data-productById");
                //     $.ajax({
                //         type: "GET",
                //         url: route,
                //         data: {
                //             id: $(this).val(),
                //         },
                //         dataType: "JSON",
                //         success: function(response) {                             
                //             $('#sap_code').val(response.sap_code)
                //             $('#material_description').val(response.description)
                //             $('#base_uom').val(response.uom)
                //             $('#product_code').val(response.product_code)
                //             $('#doctype').html(`
                //                 <option value="${response.product_type}">${response.category_description}</option>
                //             `)
                //         },
                //         error: function(request, status, error) {
                //             console.log(error);
                //         },
                //     });
                // });

                // btn konfirm edit base qty
                $("#btn-base").on("click", function(e) {
                    e.preventDefault();
                    if ($("#total").val() == "" || $("#total").val() <= 0) {
                        alert("Total berat tidak boleh kosong");
                    } else {
                        $.ajax({
                            type: "POST",
                            url: "/scaleup/total",
                            data: {
                                _token: csrftoken,
                                total: $("#total").val(),
                            },
                            dataType: "JSON",
                            success: function(response) {
                                $("#total").val(parseFloat(response.total));
                            },
                        });
                    }
                });

                // add item kategori
                $("#add-category").on("click", function(e) {
                    e.preventDefault();
                    if (
                        $("#item_category_desc").val() == "" ||
                        $("#item_category_desc").val() == null
                    ) {
                        alert(
                            "Deskripsi tidak bolerh kosong : " +
                            $("#item_category_desc").val()
                        );
                    } else {
                        if ($("#id_itemcategory_action").val() == "edit") {
                            var id = $("#id_itemcategory").val();
                            $.ajax({
                                type: "PUT",
                                url: $("#form-scaleup").attr("data-updateItemcategory"),
                                data: {
                                    _token: csrftoken,
                                    id: id,
                                    description: $("#item_category_desc").val(),
                                },
                                dataType: "JSON",
                                success: function(response) {
                                    var res = response.find((entry) => entry.id === id);

                                    var spanElement = $("#load-item").find(
                                        'span[data-itemUniq="' + id + '"]'
                                    );

                                    var h5Element = spanElement.closest("h5");

                                    renderCategory(response);

                                    // Perbarui teks dari elemen <h5> yang ditemukan
                                    h5Element
                                        .contents()
                                        .first()
                                        .replaceWith(res.description);
                                },
                                error: function(e) {
                                    console.log(e);
                                },
                            });
                        } else {
                            $.ajax({
                                type: "POST",
                                url: "/scaleup/itemCategory",
                                data: {
                                    _token: csrftoken,
                                    item_category_desc: $("#item_category_desc").val(),
                                },
                                dataType: "JSON",
                                success: function(response) {
                                    var template = "";
                                    response.forEach((r) => {
                                        template +=
                                            `<option value="${r.id}">${r.description}</option>`;
                                    });

                                    $("#item_category").html(template);

                                    var newdata = response[response.length - 1];
                                    var newTable = `
                    <div class="mb-3 col-12">
                        <h5>${newdata.description}  <span data-itemUniq=${newdata.id} title="Edit" class="flex gap-3"><i role="button" aria-hidden="true" class="fa fa-pencil-alt fa-xs p-2 edit-itemCategory"></i>
                        <i role="button" aria-hidden="true" class="fa fa-trash-alt fa-xs p-2 delete-itemCategory"></i>
                        </span></h5>
                        <table class='table table-striped w-100'>
                            <thead>
                                <tr>
                                    <th width="10%">Kode SAP</th>
                                    <th width="40%">Deskripsi</th>
                                    <th>Persentase</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>`;

                                    $("#load-item").append(newTable);
                                },
                            });
                        }
                    }

                    $("#subItemModal").modal("hide");
                    $("#item_category_desc").val("");
                    $("#id_itemcategory_action").val("add");
                    $("#id_itemcategory").val("");
                });

                // btn-sub-item
                $("#btn-sub-item").on("click", function(e) {
                    e.preventDefault();
                    let template = "";
                    $.ajax({
                        url: $("#form-scaleup").attr("data-sessionGetSubCategory"),
                        type: "GET",
                        dataType: "JSON",
                        success: function(response) {
                            //
                            response.forEach((r) => {
                                template +=
                                    `<li class="list-group-item">${r.description}</li>`;
                            });
                            // $('#sessionCategory').html('');
                            $("#sessionCategory").html(template);
                        },
                        error: function(request, status, error) {
                            console.log(error);
                        },
                    });
                });

                // edit item-category

                // Event listener untuk mengklik ikon edit
                $("#load-item").on("click", ".edit-itemCategory", function() {
                    var currentItem = $(this).closest("h5");
                    var itemName = currentItem.text().trim();
                    $("#item_category_desc").val(itemName);
                    $("#id_itemcategory").val(
                        $(this).closest("span").attr("data-itemUniq")
                    );
                    $("#id_itemcategory_action").val("edit");
                    $("#subItemModal").modal("show");
                });

                // Event listener untuk mengklik ikon delete
                $("#load-item").on("click", ".delete-itemCategory", function() {
                    var id = $(this).closest("span").attr("data-itemUniq");
                    if (
                        confirm(
                            "item yang ada dalam kategori ini akan ikut terhapus !! Apakah anda yakin akan tetap menghapus item kategory?" +
                            id
                        )
                    ) {
                        $.ajax({
                            url: $("#form-scaleup").attr("data-deleteItemcategory"),
                            type: "POST",
                            data: {
                                _token: csrftoken,
                                id: id,
                                _method: "delete",
                            },
                            dataType: "JSON",
                            success: function(response) {
                                renderItems(response.itemCategory, response.itemCart);
                                $("#item_category").trigger("change");
                                $("#item_category option[value='" + id + "']").remove();
                            },
                            error: function(e) {
                                console.log(e);
                            },
                        });
                    }
                });

                $("#subItemModal").on("hide.bs.modal", function() {
                    $("#id_itemcategory_action").val("add");
                    $("#item_category_desc").val("");
                    $("#id_itemcategory").val("");
                });
            });
        </script>
    @endpush
@endsection
