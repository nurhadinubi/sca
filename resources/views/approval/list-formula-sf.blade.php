@extends('layouts.template')

@section('title', 'Pending Approval')

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
                        <table id="example" class="table table-striped w-100">
                            <thead>
                                <tr>
                                    <th>No Formula Semi Finish</th>
                                    <th>Kode Produk</th>
                                    <th>Deskripsi</th>
                                    <th>Requester</th>
                                    <th>Tgl. Request</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formulaSemiFinish as $item)
                                    <tr  data-toggle="collapse" data-target="#collapse-{{ $item->id }}" data-key="{{ Crypt::encryptString($item->doc_number) }}"  class="accordion-toggle">
                                        <td>{{ $item->doc_number }}</td>
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->material_description }}</td>
                                        <td>{{ $item->requester }}</td>
                                        <td>{{ $item->created_at}}</td>
                                        <td>
                                            <a class="btn btn-sm m-0 px-2 btn-outline-primary " title="Approve / Reject"
                                                href="{{ route('sf.approve', ['id' => Crypt::encryptString($item->doc_number)]) }}"
                                                aria-label="hidden"><i class="fa fa-check"></i></a>
                                                <button class="btn btn-sm m-0 px-2 btn-outline-primary " data-bs-toggle="modal" data-bs-target="#exampleModal" title="Flow Approval">
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
                {{-- {{ $formulaSemiFinish->links() }} --}}
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
                responsive:false,
                order: [[4, 'asc']],
                scrollX:true,
                ordering: false,
                
            })

            $(document).ready(function() {
                $('.accordion-toggle').on('click', function() {
                    var target = $(this).data('target');
                    var $collapseContent = $('#exampleModal .modal-body');                   
                    var itemId = $(this).attr('data-key');                     
                    $.ajax({
                        url: ' {{ route('formulaSemifinish.getApproval') }} ',
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
