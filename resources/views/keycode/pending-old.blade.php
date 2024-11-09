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
                    <input type="text" class="form-control fa-align-right" id="start_date" name="start_date"
                        value="" required>
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
                    <tr data-toggle="collapse" data-target="#collapse-{{ $item->id }}" class="accordion-toggle">
                        <td>{{ $item->transaction }}</td>
                        <td>{{ $item->product_code }}</td>
                        <td>{{ $item->product_description }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->remark }}</td>
                        <td><i class="fa fa-plus-circle" aria-hidden="true"></i></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="hiddenRow">
                            <div id="collapse-{{ $item->id }}" class="collapse">
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


    {{ $keycode->links() }}
    {{-- {{ $scaleup->appends(['start_date' => $start_date, 'end_date' => $end_date])->links() }} --}}

    @push('custom-script')
        <script>
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
            const endDate = getQueryParam('end_date') || "31-12-9999";

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
                        var itemId = $(this).attr('data-target').split('-')[
                            1]; // Dapatkan ID item dari data-target
                        $.ajax({
                            url: ' {{ route('keycode.getApproval') }} ', // Ganti dengan endpoint yang sesuai
                            method: 'POST',
                            data: {
                                id: itemId,
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },

                            success: function(response) {
                                // Isi collapse dengan data step approval yang diterima
                                
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
                    $(this).find('.fa').toggleClass('fa-plus-circle fa-minus-circle'); // Ganti ikon plus/minus
                });
            });
        </script>
    @endpush
@endsection
