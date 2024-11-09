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
                        action="{{ route('scaleup.store') }}" method="POST"
                        data-getMaterial="{{ route('api.getMaterial') }}"
                        data-getMaterialById="{{ route('api.getMaterialById') }}"
                        data-getProducts = "{{ route('getProducts') }}"
                        data-getItemById = "{{ route('scaleup.getItemById') }}"
                        data-getItemcategory = "{{ route('scaleup.getItemcategory') }}"
                        data-sessionGetSubCategory = "{{ route('scaleup.sessionGetSubCategory') }}"
                        data-updateItemcategory = "{{ route('scaleup.updateItemcategory') }}"
                        data-deleteItemcategory = "{{ route('scaleup.deleteItemcategory') }}"
                        data-deleteItem = "{{ route('scaleup.deleteItem') }}">
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
                                                <label for="IssueDate" class="form-label">Tanggal terbit</label>
                                                <input type="text"
                                                    class="form-control bg-white @error('IssueDate') is-invalid @enderror"
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
                                                <label for="DocDate" class="form-label">Document Date</label>
                                                <input type="text" class="form-control" id="DocDate" name="DocDate"
                                                    value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="doctype" class="form-label">Type</label>
                                                <select name="doctype" id="doctype" class="form-select">
                                                    @foreach ($doctype as $item)
                                                        <option
                                                            {{ old('doctype') == $item->sub_category_id ? 'selected' : '' }}
                                                            value="{{ $item->sub_category_id }}">
                                                            {{ $item->code . ' - ' . $item->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="material_code" class="form-label">Material</label>
                                                <select name="material_code" id="material_code"
                                                    class="form-control  @error('material_code') is-invalid	@enderror"
                                                    required>
                                                </select>

                                                @error('material_code')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                                <input id="material_name" name="material_name" value="" hidden>
                                                <input id="sap_code" name="sap_code" value="" hidden>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="product_code" class="form-label">Kode Produk CI</label>
                                                <select name="product_code" id="product_code"
                                                    class="form-control  @error('product_code')
																							is-invalid
																					@enderror"
                                                    required>
                                                </select>
                                                @error('product_code')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="revisi" class="form-label">Revisi</label>
                                                <input type="text"
                                                    class="form-control bg-white @error('record')
																							is-invalid
																					@enderror"
                                                    id="revisi" name="revisi"
                                                    value="{{ old('revisi') ? old('revisi') : '' }}"
                                                    placeholder="Revisi" />

                                                @error('revisi')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="total" class="form-label">Total berat (KG)</label>
                                                <div class="input-group">
                                                    <input type="number" step=".001" min="1"
                                                        class="form-control  @error('total') is-invalid	@enderror"
                                                        id="total" name="total"
                                                        value="{{ session()->has('headerData.total') ? session('headerData.total') : '' }}"
                                                        placeholder="Total berat" required />
                                                    <input class="input-group-text w-25" type="text" value="KG"
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
                                                <label for="per_pack" class="form-label">Berat Perkemasan</label>
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
                                                <label for="remark" class="form-label">Keterangan</label>
                                                <textarea class="form-control bg-white" name="remark" id="remark">{{ old('remark') ? old('remark') : '' }}</textarea>
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
                                    <h3 class="card-label">Item List</h3>
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
                                                    {{-- <th>No</th> --}}
                                                    <th width="10%">Kode SAP</th>
                                                    <th width="40%">Deskripsi</th>
                                                    <th>Persentase</th>
                                                    <th>Qty</th>
                                                    <th>Unit</th>
                                                    {{-- <th>Remarks</th> --}}
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($itemCart))
                                                    @foreach ($itemCart as $item)
                                                        @if ($item['item_category'] == $i['id'])
                                                            <tr>
                                                                <td>{{ $item['item_code'] }}</td>
                                                                <td>{{ $item['item_description'] }}</td>
                                                                <td class="percent">{{ $item['percent'] }} %</td>
                                                                <td class="qty">{{ $item['qty'] }}</td>
                                                                <td>{{ $item['uom'] }}</td>
                                                                {{-- <td>{{ $item['item_remark'] }}</td> --}}
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
                                        </table>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary float-end" type="submit">Submit form</button>
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
                            <label for="item" class="form-label block">Pilih Item</label>
                            <select name="item" id="item" class="form-select w-full block">
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="percent" class="form-label">Persentase (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control bg-white" id="percent" name="percent"
                                    step="0.1" min="0" value="" placeholder="Persentase" required />
                                <span class="input-group-text" id="addon-percent">%</span>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <label for="qty" class="form-label">Qty</label>
                            <input type="number" class="form-control bg-white" id="qty" name="qty"
                                value="" placeholder="Qty" required readonly />
                        </div>

                        <div class="col-md-4">
                            <label for="uom" class="form-label">Unit</label>
                            <input type="text" class="form-control bg-white" id="uom" name="uom"
                                value="KG" placeholder="Uom" required />
                        </div>

                        <div class="col-md-12">
                            <label for="item_remark" class="form-label">Keterangan</label>
                            <input type="text" class="form-control bg-white" id="item_remark" name="item_remark"
                                value="" placeholder="Text" />
                        </div>


                        <div class="col-md-12">
                            <label for="item_category" class="form-label">Item Kategori</label>
                            <select name="item_category" id="item_category" class="form-select">

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
                            <input type="text" class="form-control bg-white" id="item_category_desc"
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
                const dataContainer = document.getElementById("data-container");
                const oldMaterial = @json(old('material_code'));
                const oldItem = @json(old('item'));

                var csrftoken = $('meta[name="csrf-token"]').attr("content");

                const path = $("#form-scaleup").attr("data-getMaterial");
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
                            console.log(response);
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
                                    parseFloat($("#total").val())) /
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
                                console.log(response);

                                renderItems(itemCategory, response);
                            },
                            error: function(error) {
                                console.log(error);
                            },
                        });
                    }
                }

                // render table
                function renderItems(itemCategory, itemCart) {
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
                                
                                    totalPercent += parseFloat(item.percent);
                                    totalQty += parseFloat(item.qty);
                                    tbody.append(row);
                                });
                                const subTotalRow =$("<tr>").append(`                                        
                                        <td colspan='2' class='text-center'>Subtotal</td>
                                        <td class='percent'>${totalPercent}%</td>
                                        <td class='qty'>${totalQty}</td>
                                        <td></td>
                                        <td></td>

                                    `)

                                    tbody.append(subTotalRow);
                            }



                            // console.log(totalPercent,'%')

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
                    qty = parseFloat(  $("#total").val() * (parseFloat(e.target.value) / 100) );
                    console.log(qty);
                    $("#qty").val(qty);
                });

                function updateQty() {
                    var total = parseFloat($("#total").val());

                    $(".percent").each(function() {
                        // console.log($(this).text().replace('%',''))
                        var percentage = parseFloat($(this).text().replace("%", ""));

                        var qty = (percentage / 100) * total;
                       
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
                                    res.material_code ? res.material_code : ""
                                } - ${res.material_description}`,
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
                                    </li>`;
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

                selctMaterial("#material_code", "body", oldMaterial);
                selctMaterial("#item", "#select-modal", oldItem);

                $("#item").on("select2:selecting", function() {
                    $("#uom").val("");
                    $("#item_code").val("");
                    $("#item_description").val("");
                });

                $("#material_code").on("select2:select", function() {
                    const route = $("#form-scaleup").attr("data-getMaterialById");
                    $("#sap_code").val(
                        $("#material_code option:selected").text().split(" - ")[0]
                    );
                    $("#material_name").val(
                        $("#material_code option:selected").text().split(" - ")[1]
                    );

                    $.ajax({
                        type: "GET",
                        url: route,
                        data: {
                            id: $(this).val(),
                        },
                        dataType: "JSON",
                        success: function(response) {
                            $("#base_uom").val(response.material_uom);
                        },
                        error: function(request, status, error) {
                            console.log(error);
                        },
                    });
                });

                $("#item").on("select2:select", function() {
                    if (
                        $(this).val() != null &&
                        $(this).val() != "" &&
                        typeof $(this).val() != "undefined"
                    ) {
                        const route = $("#form-scaleup").attr("data-getMaterialById");

                        $.ajax({
                            type: "GET",
                            url: route,
                            data: {
                                id: $(this).val(),
                            },
                            dataType: "JSON",
                            success: function(response) {
                                // console.log(response);
                                $("#uom").val(response.material_uom);
                                $("#item_code").val(response.material_code);
                                $("#item_description").val(response.material_description);
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
                    } else {
                        $("#staticBackdrop").modal("show");

                    }
                });

                // Tambah atau edit item
                $("#add").click(function(e) {
                    e.preventDefault();
                    let status = true;
                    if (
                        typeof $("#item_description").val() == "undefined" ||
                        $("#item_description").val() == ""
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
                        $("#item_category").val() == ""
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
                                console.log(itemCategory);

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
                var prodCode = $("#product_code");
                prodCode.select2({
                    placeholder: "Pilih Kode Products",
                    // dropdownParent: parent,
                    theme: "bootstrap-5",
                    ajax: {
                        url: $("#form-scaleup").attr("data-getProducts"),
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
                            return `<a href="/pc/create" type="button"
                                    class="btn btn-primary w-100" 
                                    onClick='pc()'>+ Tambah Kode Produk</a>
                </li>`;
                        },
                    },

                    escapeMarkup: function(markup) {
                        return markup;
                    },
                });

                // $('#select_category').select2({
                //     placeholder: "Pilih Kategori Item",
                //     // dropdownParent: parent,
                //     theme: "bootstrap-5",
                //     ajax: {
                //         url: $("#form-scaleup").attr("data-getItemcategory"),
                //         type: "GET",
                //         dataType: "json",
                //         casesensitive: false,
                //         processResults: (data) => {
                //             return {
                //                 results: data.map((res) => {
                //                     return {
                //                         text: res.description,
                //                         id: res.id,
                //                     };
                //                 }),
                //             };
                //         },
                //     },

                // })

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
                                    console.log(response);
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
                                    console.log(response);

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
                    // console.log($(this).closest('span').attr('data-itemUniq'))
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
                                console.log(response);
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