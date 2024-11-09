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
                    <input type="text" class="form-control fa-align-right" id="start_date" name="start_date" value=""
                    required>
                </div>
            </div>

            <div class="col-md-3 mb-3 row">
                <label for="end_date" class="col-form-label col-sm-2">To</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="end_date" name="end_date" value="" required>
                </div>
            </div>

            <div class="col-md-1 mb-3 row">
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
                <th>Material</th>
                <th>Tanggal Dokumen</th>
                <th>Tanggal Terbit</th>
                <th>Status</th>
                <th>Posisi Approver</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- @dd($scaleup) --}}
            @if ($scaleup)
                @foreach ($scaleup as $item)
                    <tr data-toggle="collapse" data-target="#collapse-{{ $loop->iteration }}" data-key="{{ $item->doc_number }}" class="accordion-toggle">
                        <td>{{ Crypt::decryptString($item->doc_number) }}</td>
                        <td>{{ $item->product_code . ' - ' . $item->material_description }}</td>
                        {{-- <td>{{ $item->material_code }}</td> --}}
                        <td>{{ \Carbon\Carbon::parse($item->doc_date)->format('d-m-Y') }}</td>
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
                            {{-- <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Detail"
                                href="{{ route('scaleup.show', ['id' => $item->doc_number]) }}" aria-label="hidden"><i
                                    class="fa fa-eye"></i></a> --}}
                                    <span class="btn btn-sm fa fa-plus-circle" aria-hidden="true"></span>
                            
                        </td>

                    </tr>
                    <tr>
                        <td colspan="7" class="hiddenRow">
                            <div id="collapse-{{ $loop->iteration }}" class="collapse">
                                <!-- Konten untuk step approval akan ditambahkan di sini melalui AJAX -->
                                <div class="step-approval-content">
                                    Loading...
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {{-- {{ $scaleup->links() }} --}}
    {{-- {{ $scaleup->appends(['start_date' => $start_date, 'end_date' => $end_date])->links() }} --}}

    @push('custom-script')
        <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script>

        let table = new DataTable('#example',{
            responsive:true
        })
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        // Get today's date
        const today = new Date();

        // Calculate date 30 days before today
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(today.getDate() - 30);

        // Format date as DD-MM-YYYY
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        // Get start_date and end_date from URL or set default
        const startDate = getQueryParam('start_date') || formatDate(thirtyDaysAgo);
        const endDate = getQueryParam('end_date') || formatDate(today);

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
                    var $collapseContent = $(target).find('.step-approval-content');

                    // Jika konten belum dimuat, lakukan permintaan AJAX
                    if (!$collapseContent.data('loaded')) {
                        var itemId = $(this).attr('data-key'); // Dapatkan ID item dari data-target
                        console.log(itemId);
                        $.ajax({
                            url: ' {{ route('scaleup.getApproval') }} ', // Ganti dengan endpoint yang sesuai
                            method: 'POST',
                            data: {
                                id: itemId,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },

                            success: function(response) {
                                // Isi collapse dengan data step approval yang diterima
                                console.log("seuucee : ", response);
                                $collapseContent.html(response);
                                $collapseContent.data('loaded',
                                    true); // Tandai sebagai telah dimuat
                            },
                            error: function(error) {
                                console.log(error)
                                $collapseContent.html('<p>Error loading data</p>');
                            }
                        });
                    }

                    // Toggle collapse
                    $(target).collapse('toggle');
                    $(this).find('span.fa').toggleClass('fa-plus-circle fa-minus-circle'); // Ganti ikon plus/minus
                });
            });
        </script>
    @endpush
@endsection
