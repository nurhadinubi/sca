@extends('layouts.template')

@section('title', $title ?? 'List Scale Up Aktif')
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

    </div>

    <form action="" method="get">
        <div class="row">
            <div class="col-md-3 mb-3 row">
                <label for="start_date" class="col-form-label col-sm-2">From </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="start_date" name="start_date"
                        value="" required>
                </div>
            </div>

            <div class="col-md-3 mb-3 row">
                <label for="end_date" class="col-form-label col-sm-2">To</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="end_date" name="end_date" value="" required>
                </div>
            </div>

            <div class="col-lg-2 mb-3 row">
                <button type="submit" class="btn btn-outline-primary inline-block">Filter</button>
            </div>
        </div>
    </form>
    <hr>

    <table id="example" class="table table-striped w-100">
        <thead>
            <tr>
                {{-- <th>No</th> --}}
                <th>No Dokumen</th>
                {{-- <th>Keycode</th> --}}
                <th>Kode Produk</th>
                <th>Deskripsi</th>
                <th>Tanggal Request</th>
                <th>Tanggal Terbit</th>
                <th>Status</th>
                <th>Posisi Approver</th>
                <th>Aksi</th>
                <th>Flow</th>
            </tr>
        </thead>
        <tbody>
            {{-- @dd($scaleup) --}}
            @if ($scaleup)
                @foreach ($scaleup as $item)
                    <tr data-toggle="collapse" data-target="#collapse-{{ $loop->iteration }}"
                        data-key="{{ $item->doc_number }}" class="accordion-toggle">
                        <td>{{ Crypt::decryptString($item->doc_number) }}</td>
                        {{-- <td>{{ $item->keycode }}</td> --}}
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->material_description }}</td>
                        {{-- <td>{{ $item->material_code }}</td> --}}
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->issue_date)->format('d-m-Y') }}</td>
                        <td>
                            @if ($item->status == 'P')
                                <span class="badge text-bg-warning"> PENDING </span>
                            @elseif($item->status == 'A')
                                <span class="badge text-bg-info"> APPROVED </span>
                            @elseif($item->status == 'R')
                                <span class="badge text-bg-danger"> REJECTED </span>
                            @endif
                        </td>

                        <td>{{ $item->approver_name }}</td>

                        <td class="">
                            @if ($item->status == 'P')
                                <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Edit"
                                    href="{{ route('scaleup.edit', ['id' => $item->doc_number]) }}" aria-label="hidden"><i
                                        class="fa fa-edit"></i></a>
                            @endif
                            

                        </td>

                        <td>
                            <span class="btn btn-sm fa fa-plus-circle" aria-hidden="true" type="button"
                                data-bs-toggle="modal" data-bs-target="#exampleModal"></span>

                        </td>
                    </tr>

                @endforeach
            @endif
        </tbody>
    </table>


    <div class="modal fade modal-xl" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Approval Flow</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Loading
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    {{-- {{ $scaleup->links() }} --}}
    {{-- {{ $scaleup->appends(['start_date' => $start_date, 'end_date' => $end_date])->links() }} --}}

    @push('custom-script')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script>
            let table = new DataTable('#example', {
                responsive:false,
                order: [[4, 'asc']],
                scrollX:true,
                
            })

            function getQueryParam(param) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            // Get today's date
            const today = new Date();

            // Calculate date 30 days before today
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 60);

            // Format date as DD-MM-YYYY
            function formatDate(date) {
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}-${month}-${year}`;
            }

            // Get start_date and end_date from URL or set default
            const startDate = getQueryParam('start_date') || formatDate(thirtyDaysAgo);
            const endDate = getQueryParam('end_date') || '31-12-9999';

            // Initialize Flatpickr with these dates
            flatpickr("#start_date", {
                defaultDate: startDate,
                dateFormat: "d-m-Y"
            });

            flatpickr("#end_date", {
                defaultDate: endDate,
                dateFormat: "d-m-Y"
            });

            $(document).ready(function() {
                $('.accordion-toggle').on('click', function() {
                    var target = $(this).data('target');
                    
                    var $collapseContent = $('#exampleModal .modal-body');
                  
                    var itemId = $(this).attr('data-key'); 
                    $.ajax({
                        url: ' {{ route('scaleup.getApproval') }} ', 
                        method: 'POST',
                        data: {
                            id: itemId,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },

                        success: function(response) {
                            
                            $collapseContent.html(response);
                            $collapseContent.data('loaded',
                                true); 
                        },
                        error: function(error) {
                            console.log(error)
                            $collapseContent.html('<p>Error loading data</p>');
                        }
                    });

                });
            });
        </script>
    @endpush
@endsection
