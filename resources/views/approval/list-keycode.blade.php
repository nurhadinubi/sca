@extends('layouts.template')

@section('title', 'Approve Keycode')

@section('content')
    <div class="row">
        @if (session('message'))
            @include('components.flash', [
                'type' => session('message')['type'],
                'text' => session('message')['text'],
            ])
        @endif

        <div class="col">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table mb-0 w-100" id="example">
                            <thead>
                                <tr>
                                    <th>Transaksi</th>
                                    <th>Kode Produk</th>
                                    <th>Deskripsi</th>
                                    <th>Requester</th>
                                    <th>Tgl. request</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($keycode as $item)
                                    <tr  data-toggle="collapse" data-target="#collapse-{{ $item->id }}" data-key="{{ $item->id }}"
                                        class="accordion-toggle">
                                        <td>{{ $item->transaction }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_description }}</td>
                                        <td>{{ $item->requester }}</td>
                                        <td data-sort="{{ $item->created_at }}">
                                            {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                                        <td>
                                            <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Approve / Reject"
                                                href="{{ route('keycode.approve', ['id' => Crypt::encryptString($item->id)]) }}"
                                                aria-label="hidden"><i class="fa fa-check"></i></a>
                                            <button class="btn btn-sm m-0 px-2 btn-outline-primary " data-bs-toggle="modal" data-bs-target="#exampleModal" title="Approval">
                                                <i class="fa fa-eye" aria-hidden="true" role="button"
                                                    ></i>
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- end card body -->
                {{-- {{ $keycode->links() }} --}}
            </div>
        </div>
    </div>

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


    @push('custom-script')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

        <script>
            let table = new DataTable('#example', {
                order: [
                    [4, 'desc']
                ],
                scrollX: true,
            })

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
