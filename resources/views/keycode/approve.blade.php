@extends('layouts.template')

@section('title', 'Approve Keycode ' )

@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                  <div class="row">
                    <div class="col-12 mb-3">
                      Permintaan Approval Keycode untuk transaksi sbb :
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-2">Produk</div>
                    <div class="col-10"><strong>:{{ $keycode->product_code ." - ". $keycode->product_description }}</strong></div>
                  </div>
                  <div class="row">
                    <div class="col-2">Requester</div>
                    <div class="col-10">:{{ $keycode->requester}}</div>
                  </div>
                </div>
                <hr />
                <div class="row">
                    <span>Alasan request : </span>
                    <div> {{ $keycode->remark }} </div>
                </div>


                <hr />
								
                @if ($valid)
                    <form class="mt-4" action="{{ route('keycode.approveStore', ['id'=>Crypt::encryptString($approver->id)]) }}" method="post">
                        @csrf
                        <div class="col mb-3">
                            <label for="note" class="form-label">Catatan Approver</label>
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
											<div class="btn btn-outline-primary">Dokumen sudah anda Approve <br/> {{ \Carbon\carbon::parse($approver->updated_at)->format('d-M-Y , H:m:s') }} </div>
									@elseif($approver->status =='R')
											<div class="btn btn-outline-danger">Dokumen Sudah anda reject  <br/> {{ \Carbon\carbon::parse($approver->updated_at)->format('d-M-Y , H:m:s')}} </div>
									@else
											<div class="btn btn-outline-info">Dokumen menunggu approval sebelum anda</div>
									@endif
                @endif

            </div>
        </div>
    </div>
@endsection
