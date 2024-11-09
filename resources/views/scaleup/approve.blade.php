@extends('layouts.template')

@section('title', 'Approve Scaleup - ' . $header->doc_number)

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="row">
                            <span class="col-sm-3">No Dokumen</span>
                            <span class="col-sm-9">: {{ $header->doc_number }}</span>
                        </div>
                        <div class="row">
                            <span class="col-sm-3">Kode Produk</span>
                            <span class="col-sm-9">:
                                {{ $header->product_code . ' - ' . $header->material_description }}</span>
                        </div>

                        <div class="row">
                            <span class="col-sm-3">Total</span>
                            <span class="col-sm-9">: {{ $header->total . ' ' . strtolower($header->base_uom) }}</span>
                        </div>
                        <div class="row">
                            <span class="col-sm-3">Revisi</span>
                            <span class="col-sm-9">: {{ $header->revision ? $header->revision : '0' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <span class="col-sm-3">Doc. Date</span>
                            <span class="col-sm-9">: {{ \Carbon\Carbon::parse($header->doc_date)->format('d-M-Y') }}</span>
                        </div>
                        <div class="row">
                            <span class="col-sm-3">Tanggal Terbit</span>
                            <span class="col-sm-9">:
                                {{ \Carbon\Carbon::parse($header->issue_date)->format('d-M-Y') }}</span>
                        </div>


                        <div class="row">
                            <span class="col-sm-3">Berat Per Kemasan</span>
                            <span class="col-sm-9">: {{ $header->per_pack }}</span>
                        </div>
                        <div class="row">
                            <span class="col-sm-3">Halaman</span>
                            <span class="col-sm-9">: {{ $header->halaman }}</span>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <span>remark dokumen</span>
                    <div> {{ $header->remark }} </div>
                </div>
                <hr />

                <h5 class="text-center">Item</h5>
                @php
                    $grandTotalQty = 0;
                    $grandTotalPercent = 0;
                @endphp
                @if (count($itemCategory))
                    @foreach ($itemCategory as $i)
                        <div class="mb-3 col-12">
                            <h5>{{ $i->description }} </h5>
                            <table class="table table-bordered w-100 table-sm">
                                <thead>
                                    <tr>
                                        {{-- <th>No</th> --}}
                                        <th width="10%">Kode SAP</th>
                                        <th width="40%">Deskripsi</th>
                                        <th>Persentase</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Remarks</th>
                                        {{-- <th>Action</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $subtotalQty = 0;
                                        $subtotalPercent = 0;
                                    @endphp
                                    @if (count($detail))
                                        @foreach ($detail as $item)
                                            @if ($item->category_reference == $i->uniqid)
                                            @php
                                                 $subtotalPercent += $item->percent;
                                                 $subtotalQty += ($item->percent*$header->total/100);
                                            @endphp
                                                <tr>
                                                    <td>{{ $item->material_code }}</td>
                                                    <td>{{ $item->material_description }}</td>
                                                    <td>{{ $item->percent }} %</td>
                                                    <td>{{ number_format(($header->total * $item->percent) / 100,3) }}</td>
                                                    <td>{{ $item->uom }}</td>
                                                    <td>{{ $item->item_remark }}</td>

                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-center">Subtotal:</th>
                                        <th class="percent">{{ $subtotalPercent }} %</th>
                                        <th class="qty">{{number_format($subtotalQty,3) }}</th>
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
                    <div class="col-12 mt-1">
                        <table class="table table-bordered w-100">
                            <tfoot>
                                <tr>
                                    <th width="50%" colspan="2" class="text-center">Total Keseluruhan:
                                    </th>
                                    <th class="percent">{{ $grandTotalPercent }} %</th>
                                    <th class="qty">{{ number_format($grandTotalQty,3) }}</th>
                                    <th></th>
                                    <th></th>
                                    

                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif

                @if ($valid)
                    <form class="mt-4"
                        action="{{ route('scaleup.approvestore', ['id' => Crypt::encryptString($approver->id)]) }}"
                        method="post">
                        @csrf
                        <div class="col mb-3">
                            <label for="note" class="form-label">Keterangan</label>
                            <textarea class="form-control bg-white" name="note" id="note">{{ old('note') ? old('note') : '' }}</textarea>
                        </div>


                        <div class="grid gap-4">
                            <button type="submit" name="action" value="R"
                                class="btn btn-outline-warning">Reject</button>
                            <button type="submit" name="action" value="A" class="btn btn-primary">Approve</button>
                        </div>
                    </form>
                @else
                    @if ($approver->status == 'A')
                        <div class="btn btn-outline-primary">Dokumen sudah anda Approve <br />
                            {{ \Carbon\carbon::parse($approver->updated_at)->format('d-M-Y , h:m:s') }} </div>
                    @elseif($approver->status == 'R')
                        <div class="btn btn-outline-danger">Dokumen Sudah anda reject <br />
                            {{ \Carbon\carbon::parse($approver->updated_at)->format('d-M-Y , h:m:s') }} </div>
                    @else
                        <div class="btn btn-outline-info">Dokumen menunggu approval sebelum anda</div>
                    @endif
                @endif

            </div>
        </div>
    </div>
@endsection
{{-- 
    Saat Approve, Note nya muncul di page show.
--}}
