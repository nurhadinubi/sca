@extends('layouts.template')

@section('title', $title ?? 'List Keycode Pending')
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
                    <input type="text" class="form-control" id="start_date" name="start_date" value="" required>
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

                <th>Transaksi</th>
                <th>Produk Kode</th>
                <th>Deskripsi</th>
                <th>Tanggal Request</th>
                <th>Remark</th>
                <th>Flow</th>
            </tr>
        </thead>
        <tbody>
            @if ($keycode)
                @foreach ($keycode as $item)
                    <tr data-toggle="collapse" data-target="#collapse-{{ $item->id }}" data-key="{{ $item->id }}"
                        class="accordion-toggle">
                        <td>{{ $item->transaction }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_description }}</td>
                        <td data-sort="{{ $item->created_at }}">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y , H:m:s') }}</td>
                        <td>{{ $item->remark }}</td>
                        <td><i class="fa fa-plus-circle" aria-hidden="true" role="button" data-bs-toggle="modal"
                                data-bs-target="#exampleModal"></i></td>
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


    {{-- {{ $keycode->links() }} --}}
    {{-- {{ $scaleup->appends(['start_date' => $start_date, 'end_date' => $end_date])->links() }} --}}
    @push('custom-script')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script>
            let table = new DataTable('#example', {
                responsive: true,
                order: [[3, 'desc']],
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
                    // var $collapseContent = $(target).find('.step-approval-content');
                    var $collapseContent = $('#exampleModal .modal-body');
                    // collapseContent.html('');
                    // Jika konten belum dimuat, lakukan permintaan AJAX
                    var itemId = $(this).attr('data-key'); // Dapatkan ID item dari data-target

                    $.ajax({
                        url: ' {{ route('keycode.getApproval') }}', // Ganti dengan endpoint yang sesuai
                        method: 'POST',
                        data: {
                            id: itemId,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },

                        success: function(response) {
                            // Isi collapse dengan data step approval yang diterima
                            console.log(response);
                            $collapseContent.html(response);
                            $collapseContent.data('loaded',
                                true); // Tandai sebagai telah dimuat
                        },
                        error: function(error) {
                            console.log(error)
                            $collapseContent.html('<p>Error loading data</p>');
                        }
                    });


                    // Toggle collapse
                    // $(target).collapse('toggle');
                    // $(this).find('span.fa').toggleClass('fa-plus-circle fa-minus-circle');
                });
            });
        </script>
    @endpush
@endsection
