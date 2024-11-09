flatpickr("#IssueDate", {
    dateFormat: "d-m-Y",
    altFormat: "d-m-Y",
    defaultDate: "today",
});

document.addEventListener("DOMContentLoaded", function () {
    var toggleIcon = document.getElementById("toggleIcon");
    var collapseElement = document.getElementById("collapseExample");

    collapseElement.addEventListener("show.bs.collapse", function () {
        toggleIcon.classList.remove("fa-caret-right");
        toggleIcon.classList.add("fa-caret-down");
    });

    collapseElement.addEventListener("hide.bs.collapse", function () {
        toggleIcon.classList.remove("fa-caret-down");
        toggleIcon.classList.add("fa-caret-right");
    });
});

$(document).ready(function () {
    const dataContainer = document.getElementById("data-container");
    const oldMaterial = JSON.parse(dataContainer.getAttribute("data-material"));
    const oldItem = JSON.parse(dataContainer.getAttribute("data-item"));

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
            success: function (response) {
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
                    (parseFloat(response.percent).toFixed(3) *
                        parseFloat($("#total").val()).toFixed(3)) /
                        100
                );
                $("#uom").val(response.uom);
                $("#item_remark").val(response.item_remark);
                $("#uniqid").val(response.uniqid);

                $("#item_category option").each(function () {
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
                success: function (response) {
                    var itemCategory = $("#item_category option")
                        .map(function () {
                            return {
                                id: this.value,
                                text: $(this).text(),
                            };
                        })
                        .get();
                    console.log(response);

                    renderItems(itemCategory, response);
                },
                error: function (error) {
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
                        tbody.append(row);
                    });
                }

                table.append(tbody);
                categoryDiv.append(table);
                loadItemDiv.append(categoryDiv);
            });

            $(".editItem").click(function (e) {
                e.preventDefault();
                editItemEvent(this);
            });

            $(".DeleteButton").on("click", function (e) {
                e.preventDefault();
                deleteItemEvent(this);
            });
        }
    }

    function renderCategory(itemCategory) {
        var categoriEl = $("#item_category");
        categoriEl.html("");
        var temp = "";
        itemCategory.forEach(function (i) {
            temp += `<option value='${i.id}'>${i.description} </option>`;
        });

        categoriEl.html(temp);
    }
    // event persentase dirubah
    $("#percent").on("change", function (e) {
        e.preventDefault();
        var qty = 0;
        qty = parseFloat(
            $("#total").val() * (parseFloat(e.target.value) / 100)
        ).toFixed(3);
        console.log(qty);
        $("#qty").val(qty);
    });

    function updateQty() {
        var total = parseFloat($("#total").val());

        $(".percent").each(function () {
            // console.log($(this).text().replace('%',''))
            var percentage = parseFloat($(this).text().replace("%", ""));

            var qty = (percentage / 100) * total;
            $(this).closest("tr").find(".qty").text(qty.toFixed(2));
        });
    }
    // Field Total diubah
    $("#total").on("change", function (e) {
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
                success: function (response) {
                    $("#total").val(parseFloat(response.total).toFixed(3));
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
                noResults: function () {
                    return `<a href="/ci/create" type="button"
                                    class="btn btn-primary w-100" 
                                    onClick='task()'>+ Tambah Material</a>
                                    </li>`;
                },
            },

            escapeMarkup: function (markup) {
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
                success: function (response) {
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
                error: function (request, status, error) {
                    console.log(error);
                },
            });
        }
    }

    selctMaterial("#material_code", "body", oldMaterial);
    selctMaterial("#item", "#select-modal", oldItem);

    $("#item").on("select2:selecting", function () {
        $("#uom").val("");
        $("#item_code").val("");
        $("#item_description").val("");
    });

    $("#material_code").on("select2:select", function () {
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
            success: function (response) {
                $("#base_uom").val(response.material_uom);
            },
            error: function (request, status, error) {
                console.log(error);
            },
        });
    });

    $("#item").on("select2:select", function () {
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
                success: function (response) {
                    // console.log(response);
                    $("#uom").val(response.material_uom);
                    $("#item_code").val(response.material_code);
                    $("#item_description").val(response.material_description);
                },
                error: function (request, status, error) {
                    console.log(error);
                },
            });
        }
    });

    // button Di tambah item di klik
    $("#btn-add-item").on("click", function (e) {
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
            return;
        }
    });

    // Tambah atau edit item
    $("#add").click(function (e) {
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
                success: function (response) {
                    console.log(response);

                    var itemCategory = $("#item_category option")
                        .map(function () {
                            return {
                                id: this.value,
                                text: $(this).text(),
                            };
                        })
                        .get();
                    console.log(itemCategory);

                    renderItems(itemCategory, response);
                },
                error: function (error) {
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
    $(".editItem").click(function (e) {
        e.preventDefault();
        editItemEvent(this);
    });

    // Delete Item
    $(".DeleteButton").on("click", function (e) {
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
            noResults: function () {
                return `<a href="/pc/create" type="button"
                                    class="btn btn-primary w-100" 
                                    onClick='pc()'>+ Tambah Kode Produk</a>
                </li>`;
            },
        },

        escapeMarkup: function (markup) {
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
    $("#btn-base").on("click", function (e) {
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
                success: function (response) {
                    $("#total").val(parseFloat(response.total).toFixed(3));
                },
            });
        }
    });

    // add item kategori
    $("#add-category").on("click", function (e) {
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
                    success: function (response) {
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
                    error: function (e) {
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
                    success: function (response) {
                        console.log(response);

                        var template = "";
                        response.forEach((r) => {
                            template += `<option value="${r.id}">${r.description}</option>`;
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
    $("#btn-sub-item").on("click", function (e) {
        e.preventDefault();
        let template = "";
        $.ajax({
            url: $("#form-scaleup").attr("data-sessionGetSubCategory"),
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                //
                response.forEach((r) => {
                    template += `<li class="list-group-item">${r.description}</li>`;
                });
                // $('#sessionCategory').html('');
                $("#sessionCategory").html(template);
            },
            error: function (request, status, error) {
                console.log(error);
            },
        });
    });

    // edit item-category

    // Event listener untuk mengklik ikon edit
    $("#load-item").on("click", ".edit-itemCategory", function () {
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
    $("#load-item").on("click", ".delete-itemCategory", function () {
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
                success: function (response) {
                    console.log(response);
                    renderItems(response.itemCategory, response.itemCart);
                    $("#item_category").trigger("change");
                    $("#item_category option[value='" + id + "']").remove();
                },
                error: function (e) {
                    console.log(e);
                },
            });
        }
    });
    $("#subItemModal").on("hide.bs.modal", function () {
        $("#id_itemcategory_action").val("add");
        $("#item_category_desc").val("");
        $("#id_itemcategory").val("");
    });
});
