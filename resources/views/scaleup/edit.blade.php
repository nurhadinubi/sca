@extends('layouts.template')

@section('title', $title??'Edit Scale Up')

@section('content')
    <div class="">
        @if (session('message'))
            @include('components.flash', [
                'type' => session('message')['type'],
                'text' => session('message')['text'],
            ])
        @endif
        @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">

                    <strong>{{ $errors->all()[0] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        <div class="card">
            <div class="card-body">
                {{-- @dd($header->doc_number) --}}
                <form action="{{ route('edit.scaleup.update', ['id'=>$header->doc_number]) }}" id="form-scaleup" method="post" onkeydown="return event.key != 'Enter';"
                    data-getMaterial="{{ route('api.getMaterial') }}"
                    data-getMaterialById="{{ route('api.getMaterialById') }}" 
                    data-getProducts = "{{ route('getProducts') }}"
                    data-getItemById = "{{ route('edit.scaleup.getItemById') }}"
                    data-sessionGetSubCategory = "{{ route('edit.scaleup.sessionGetSubCategory') }}"
                    data-sessionSaveItem = "{{ route('edit.scaleup.saveItem') }}"
                    data-sessionsaveitemCategory = "{{ route('edit.scaleup.itemCategory') }}"
                    data-updateItemcategory = "{{ route('edit.scaleup.updateItemcategory') }}"
                    data-deleteItemcategory = "{{ route('edit.scaleup.deleteItemcategory') }}"
                    data-deleteItem = "{{ route('edit.scaleup.deleteItem') }}" 
                     data-sapList="{{ route('sap.list', ['type' => 'ROH1']) }}" data-sapById="{{ route('sap.byId') }}"
                    >
                    @csrf
                    <div class="row">
                        @method('PUT')
                        <div class="collapse show" id="collapseExample">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="IssueDate" class="form-label required">Tanggal terbit</label>
                                    <input type="text" class="form-control  @error('IssueDate') is-invalid @enderror"
                                        id="IssueDate" name="IssueDate"
                                        value="{{ old('IssueDate') ? \Carbon\carbon::createFromFormat('d-m-Y',old('IssueDate'))->format('d-m-Y') : \Carbon\carbon::createFromFormat( 'Y-m-d' ,$header->issue_date)->format('d-m-Y')}}"
                                        placeholder="Pilih tanggal" required>

                                    @error('IssueDate')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                               
                                <div class="col-md-3 mb-3">
                                    <label for="doc_number" class="form-label">Tanggal Dokumen</label>
                                    <input type="text" class="form-control form-read-only" id="DocDate" name="DocDate"
                                        value="{{ old('DocDate') ?\Carbon\carbon::createFromFormat('d-m-Y',old('DocDate'))->format('d-m-Y') : \Carbon\carbon::createFromFormat( 'Y-m-d' ,$header->doc_date)->format('d-m-Y') }}" required
                                        readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="doc_number" class="form-label">Nomor Dokumen</label>
                                    <input type="text" class="form-control form-read-only" id="doc_number" name="doc_number"
                                        value="{{ old('doc_number') ?(old('doc_number')):Crypt::decryptString($header->doc_number) }}" required
                                        readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="product_select" class="form-label">Kode Produk CI</label>
                                    <select name="product_select form-read-only" id="product_select"
                                        class="form-select  @error('product_select') is-invalid @enderror" required>
                                        <option selected value="{{ $header->material_id }}">
                                            {{ $header->product_code . ' - ' . $header->material_description }}</option>
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
                                        <option value="{{ $header->doctype_id }}">{{ $header->category_description }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="product_code" class="form-label">Kode Produk</label>
                                    <input type="text" class="form-control form-read-only  @error('product_code') is-invalid @enderror"
                                        id="product_code" name="product_code"
                                        value="{{ old('product_code') ? old('product_code') : $header->product_code }}"
                                        placeholder="Kode Produk CI" readonly />

                                    @error('product_code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="sap_code" class="form-label">Kode SAP</label>
                                    <input type="text" class="form-control form-read-only  @error('sap_code') is-invalid @enderror"
                                        id="sap_code" name="sap_code"
                                        value="{{ old('sap_code') ? old('sap_code') : $header->material_code }}"
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
                                        value="{{ old('material_description') ? old('material_description') : $header->material_description }}"
                                        placeholder="Nama Material" readonly />

                                    @error('material_description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="revisi" class="form-label">Revisi</label>
                                    <input type="text" class="form-control form-read-only  @error('revisi') is-invalid @enderror"
                                        id="revisi" name="revisi" value="{{ old('revisi') ? old('revisi') : '' }}"
                                        placeholder="Revisi" readonly />

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
                                            class="form-control  @error('total') is-invalid	@enderror" id="total"
                                            name="total" value="{{ old('total') ? old('total') : $header->total }}"
                                            placeholder="Total berat" required />
                                        <input class="input-group-text col-sm-3" type="text"
                                            value="{{ old('base_uom') ? old('base_uom') : $header->base_uom }}"
                                            name="base_uom" id="base_uom" />
                                        {{-- <button id="btn-base" type="button" class="input-group-text"><i
                                            class="fa fa-check text-success"></i></button> --}}
                                        @error('total')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="per_pack" class="form-label required">Berat Perkemasan</label>
                                    <input type="text" class="form-control @error('per_pack') is-invalid	@enderror"
                                        id="per_pack" name="per_pack"
                                        value="{{ old('per_pack') ? old('per_pack') : $header->per_pack }}"
                                        placeholder="Berat Perkemasan" required />
                                    @error('per_pack')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="remark" class="form-label">Keterangan</label>
                                    <textarea class="form-control " name="remark" id="remark">{{ old('remark') ? old('remark') : $header->remark }}</textarea>
                                    @error('remark')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>


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
                    {{-- Render Item --}}
                    <div id="load-item">
                        @php
                            $grandTotalQty = 0;
                            $grandTotalPercent = 0;
                        @endphp
                        @if (count($itemCategory))
                            @foreach ($itemCategory as $i)
                                <div class="mb-3 col-12">
                                    <h5>{{ $i['description'] }} <span data-itemUniq="{{ $i['id'] }}" title="Edit"
                                            class=" flex gap-3"> <i role="button" aria-hidden="true"
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
                                                <th width="10%">Unit</th>
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
                                                            $subtotalPercent += $item['percent'];
                                                            $subtotalQty += $item['percent']*($header->total?$header->total:0)/100;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $item['item_code'] }}</td>
                                                            <td>{{ $item['item_description'] }}</td>
                                                            <td class="percent">{{ number_format($item['percent'],3) }} %</td>
                                                            <td class="qty">{{ number_format($item['percent']*($header->total?$header->total:0)/100,3) }}</td>
                                                            <td>{{ $item['uom'] }}</td>
                                                            <td>{{ $item['item_remark'] }}</td>
                                                            <td class="d-flex gap-1">
                                                                <a href="#"
                                                                    class="DeleteButton btn btn-sm outline-none"
                                                                    role="button" data-uniqid="{{ $item['uniqid'] }}"
                                                                    aria-label="hidden"><i
                                                                        class="fa fa-trash text-secondary"></i></a>

                                                                <a href="#"
                                                                    class="editItem btn btn-sm  outline-none"
                                                                    role="button" data-uniqid="{{ $item['uniqid'] }}"
                                                                    aria-label="hidden"><i
                                                                        class="fa fa-edit text-info"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-center">Subtotal:</th>
                                                <th class="percent">{{ number_format($subtotalPercent,3) }} %</th>
                                                <th class="qty">{{ number_format($subtotalQty,3) }}</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                @php
                                    $grandTotalPercent += $subtotalPercent;
                                    $grandTotalQty =$grandTotalPercent*($header->total?$header->total:0)/100
                                @endphp
                            @endforeach

                            <!-- Display Grand Total -->
                            <div class="col-12 mt-4">
                                <table class="table table-bordered">
                                    <tfoot>
                                        <tr>
                                            <th width="45%" colspan="2" class="text-center">Total Keseluruhan:
                                            </th>
                                            <th width="10%" class="percent">{{number_format($grandTotalPercent,3) }} %</th>
                                            <th width="10%" class="qty">{{ number_format($grandTotalQty,3) }}</th>
                                            <th width="35%"></th>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('scaleup.index') }}" class="btn btn-outline-info">Kembali</a>
                        <button class="btn btn-primary" name="action" value="save" type="submit">Update Scaleup</button>
                    </div>
                </form>
            </div>
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
                                        step="0.1" min="0" value="" placeholder="Persentase"
                                        required />
                                    <span class="input-group-text" id="addon-percent">%</span>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <label for="qty" class="form-label">Qty</label>
                                <input type="number" class="form-control form-read-only" id="qty" name="qty"
                                    value="" placeholder="Qty" required readonly />
                            </div>

                            <div class="col-md-4">
                                <label for="uom" class="form-label required">Unit</label>
                                <input type="text" class="form-control " id="uom" name="uom"
                                    value="KG" placeholder="Uom" required />
                            </div>

                            <div class="col-md-12">
                                <label for="item_remark" class="form-label">Keterangan</label>
                                <input type="text" class="form-control " id="item_remark" name="item_remark"
                                    value="" placeholder="Text" />
                            </div>


                            <div class="col-md-12">
                                <label for="item_category" class="form-label ">Item Kategori</label>
                                <select name="item_category" id="item_category" class="form-select bg-white">
                                    @if (count($itemCategory) >= 1)
                                        @foreach ($itemCategory as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['description'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <input type="text" name="item_code" value="" id="item_code" hidden>
                        <input type="text" name="item_description" value="" id="item_description" hidden>
                        <input type="text" name="item_action" value="add" id="item_action" hidden>
                        <input type="text" name="uniqid" value="" id="uniqid" hidden>

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
                                <label for="item_category_desc" class="form-label">Item Kategori</label>
                                <input type="text" class="form-control " id="item_category_desc"
                                    name="item_category_desc" value="" placeholder="A. Saus BBQ" />
                            </div>
                        </div>

                        <input type="text" name="id_itemcategory" id="id_itemcategory" value="" readonly
                            hidden>
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

    </div>


    @push('custom-script')
        <script>
            $(document).ready(function() {
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

                function renderItems(itemCategory, itemCart) {
                    const loadItemDiv = $("#load-item");
                    loadItemDiv.empty(); // Kosongkan elemen sebelum render

                    let grandTotalPercent = 0;
                    let grandTotalQty = 0;

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
                                    <th width="10%">Remark</th>
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
                                    <td>${item.item_code??""}</td>
                                    <td>${item.item_description}</td>
                                    <td class='percent'>${item.percent.toFixed(3)} %</td>
                                    <td class='qty'>${item.qty.toFixed(3)}</td>
                                    <td>${item.uom}</td>
                                    <td>${item.item_remark??""}</td>
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
                                    <td colspan='2' width="45%" class='text-center'>Subtotal</td>
                                    <td width="10%" class='percent'>${totalPercent.toFixed(3)}%</td>
                                    <td width="10%" class='qty'>${totalQty.toFixed(3)}</td>
                                    <td width="10%"></td>
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
                        updateQty();
                    }
                });

                function selctMaterial(el, parent, old = null) {
                    const path = $("#form-scaleup").attr("data-sapList");
                    console.log(path)
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
                                return `<a href="/ci/create" type="button"
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
                                $("#uom").val(response.material_uom);
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
                        $("#percent").val() == ""||$("#percent").val() == null
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
                            url: $("#form-scaleup").attr("data-sessionSaveItem"),
                            
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
                               
                                var itemCategory = $("#item_category option")
                                    .map(function() {
                                        return {
                                            id: this.value,
                                            text: $(this).text(),
                                        };
                                    })
                                    .get();
                               
                                renderItems(itemCategory, response);
                                updateQty();
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
                                url: $("#form-scaleup").attr("data-sessionsaveitemCategory"),
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

                // btn-sub-item memunculkan sub categori
                $("#btn-sub-item").on("click", function(e) {
                    e.preventDefault();
                    let template = "";
                    $.ajax({
                        url: $("#form-scaleup").attr("data-sessionGetSubCategory"),
                        type: "GET",
                        dataType: "JSON",
                        success: function(response) {
                            console.log("Line 1008",response)
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
                                console.log( 'line 1054' ,response);
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
            })
        </script>
    @endpush
@endsection
