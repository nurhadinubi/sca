@extends('layouts.template')

@section('title', 'Compare Scale Up')
@section('content')

    <div class="row ">

        <div class="col-12">
            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
        </div>
        <div class="row" id="compare" data-listScaleUp="{{ route('scaleup.listScaleUp') }}">
            <div class="col-md-6">
                <div class="col-12 mb-3">
                    <label for="scaleup1" class="form-label">Pilih No Scale Up</label>
                    <select name="scaleup1" id="scaleup1" class="form-control ">
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <div id="head-scaleup1">

                    </div>
                </div>
                <div class="col-12">
                    <div id="item-scaleup1" class="list-group">

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-12 mb-3">
                    <label for="scaleup2" class="form-label">Pilih No Scale Up</label>
                    <select name="scaleup2" id="scaleup2" class="form-control ">
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <div id="head-scaleup2">

                    </div>
                </div>
                <div class="col-12">
                    <div id="item-scaleup2" class="list-group">

                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('custom-script')
        <script>
            $(document).ready(function() {
                var token = $('meta[name="csrf-token"]').attr("content");

                function getScaleup(element) {
                    $(element).select2({
                        placeholder: "Pilih Nomor Scale UP",
                        // dropdownParent: parent,
                        theme: "bootstrap-5",
                        ajax: {
                            url: $('#compare').attr("data-listScaleUp"),
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
                }


                getScaleup('#scaleup1');
                getScaleup('#scaleup2');

                // $('#scaleup1').on("select2:selecting", function() {
                //     $('#item-scaleup1').html("");
                //     $('#head-scaleup1').html("");
                // })

                function renderHeader(header, element) {
                    var headerEl = "";
                    console.log(header.doc_number);
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
																<span class="col-2">Iss Date</span>
																<span class="col-10">: ${header.issue_date}</span>
															</div>
															<div class="row">
																<span class="col-2">Material</span>
																<span class="col-10">: ${header.material_code + " - " + header.material_description  }</span>
															</div>
															<div class="row">
																<span class="col-2">Total</span>
																<span class="col-10">: ${header.total }</span>
															</div>

														`
                    $(element).html("");
                    $(element).html(headerEl);
                }

                function renderItem(items = [], total = 0, element) {
                    var itemEl = "";
                    items.forEach(i => {
                        itemEl += `
															<tr>	
																<td> ${i.material_description} </td>			
																<td> ${ i.percent  }% </td>			
																<td> ${ parseFloat(i.percent*total/100).toFixed(3)+ i.uom  } </td>			
																			
															</tr>
															`
                    });
                    var tableEl = `
														
														<table class="w-100 table-sm table-responsive table-bordered">
															<thead>
																<tr>
																	<td>Material</td>
																	<td>Persentase</td>
																	<td>Unit</td>
																</tr>
															</thead>

															<tbody>
																${itemEl}
															</tbody>
														</table>
														`
                    $(element).html("");
                    $(element).html(tableEl);

                }
                $('#scaleup1').on("select2:select", function() {
                    $.ajax({
                        url: "/scaleup/getByID",
                        type: "POST",
                        data: {
                            _token: token,
                            id: $(this).val(),
                        },
                        dataType: "json",
                        success: function(response) {
                            // console.log(response);
                            renderHeader(response.header, '#head-scaleup1')

                            renderItem(response.detail, response.header.total, '#item-scaleup1')


                        },
                        error: function(e) {
                            console.log(e)
                        }

                    })
                })

                $('#scaleup2').on("select2:select", function() {
                    $.ajax({
                        url: "/scaleup/getByID",
                        type: "POST",
                        data: {
                            _token: token,
                            id: $(this).val(),
                        },
                        dataType: "json",
                        success: function(response) {
                            // console.log(response);
                            renderHeader(response.header, '#head-scaleup2')

                            renderItem(response.detail, response.header.total, '#item-scaleup2')


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
