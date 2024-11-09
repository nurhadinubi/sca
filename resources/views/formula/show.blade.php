@extends('layouts.template')

@section('title', 'Detail Scale Up - ' . $header->doc_number)

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
                            <span class="col-sm-3">Material</span>
                            <span class="col-sm-9">:
                                {{ $header->material_code . ' - ' . $header->material_description }}</span>
                        </div>
                        
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <span class="col-sm-3">Doc. Date</span>
                            <span class="col-sm-9">: {{ \Carbon\Carbon::parse($header->doc_date)->format('d-M-Y') }}</span>
                        </div>
                        <div class="row">
                            <span class="col-sm-3">Tanggal Valid</span>
                            <span class="col-sm-9">:
                                {{ \Carbon\Carbon::parse($header->valid_date)->format('d-M-Y') }}</span>
                        </div>
                        
                    </div>
                </div>
                <hr />
                <div class="row">
                    <span>Remark</span>
                    <div> {{ $header->remark }} </div>
                </div>
                <hr />

                <div class="row">
                    <h5 class="text-center">Item</h5>

                   
                    
                        <div class="mb-3 col-12">
                            
                            <table class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>ICt</th>
                                        <th width="10%">Kode SAP</th>
                                        <th width="40%">Deskripsi</th>
                                        {{-- <th>Persentase</th> --}}
                                        <th>Qty</th>
                                        <th>S Loc</th>
                                        <th>Remarks</th>
                                        {{-- <th>Action</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($detail))
                                        @foreach ($detail as $item)
                                           
                                                <tr>
                                                    <td>{{ $item->item_category }}</td>
                                                    <td>{{ $item->material_code }}</td>
                                                    <td>{{ $item->material_description }}</td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>{{ $item->sloc }}</td>
                                                    <td>{{ $item->remark }}</td>
                                                </tr>
                                            
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                   
               

                </div>

                <div class="row mt-4">
                    <h5 class="text-center mb-4">Document Flow</h5>
                    <div class="container pb-5 mb-sm-4">

                        <!-- Progress-->
                        <div class="steps">
                            <div class="steps-body">
                                <div class="step step-completed">
                                    <span class="step-indicator"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-check">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                    <span class="step-icon">
                                        <i class="fa fa-file-word fa-2x"></i>
                                    </span>Document Created
                                    <div class="row mt-4">
                                        <h6> {{ $header->name }} </h6>
                                        <span class="text-xs text-muted">
                                            {{ \Carbon\carbon::parse($header->created_at)->format('d-M-Y , h:m:s') }}
                                        </span>
                                        {{-- <span> {{ dd($header) }} </span> --}}
                                    </div>
                                </div>


                                @foreach ($approval as $item)
                                    <div class="step step-completed">
                                        @if ($item->status == 'P')
                                            <span class="step-icon">
                                                <i class="fa fa-exclamation-triangle text-warning fa-2x"></i>
                                            </span>Pending Approval

                                            <div class="row mt-4">
                                                <h6> {{ $item->name }} </h6>
                                                <span class="text-xs text-muted">
                                                    {{ \Carbon\carbon::parse($header->created_at)->format('d-M-Y , h:m:s') }}
                                                </span>
                                            </div>
                                        @endif
                                        @if ($item->status == 'A')
                                            <span class="step-indicator">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-check">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </span>

                                            <span class="step-icon">
                                                <i class="fa fa-star text-info fa-2x"></i>
                                            </span>Approved

                                            <div class="row mt-4">
                                                <h6> {{ $item->name }} </h6>
                                                <p>{{ $item->note }}</p>
                                                <span class="text-xs text-muted">
                                                    {{-- ini ganti Approve at --}}
                                                    {{ \Carbon\carbon::parse($header->updated_at)->format('d-M-Y , h:m:s') }}
                                                </span>

                                            </div>
                                        @endif

																				@if ($item->status == 'R')
                                            <span class="step-icon">
                                                <i class="fa fa-times-circle text-danger fa-2x"></i>
                                            </span>Rejected

                                            <div class="row mt-4">
                                                <h6> {{ $item->name }} </h6>
                                                <span class="text-xs text-muted">
                                                    {{ \Carbon\carbon::parse($header->created_at)->format('d-M-Y , h:m:s') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach


                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('custom-style')
   <link rel="stylesheet" href="{{ asset('assets/custom/css/scaleup/show.css') }}">
@endpush
