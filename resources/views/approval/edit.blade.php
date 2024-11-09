@extends('layouts.template')

@section('title', 'Edit Approval')

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
                    {{-- @dd(session('message')) --}}
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('master.approval.put',['id'=>$approval->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label for="transaction_type" class="form-label">Tipe Transaksi</label>
                            <select name="transaction_type" id="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror"
                                required>
                                <option {{ $approval->transaction_type=='scaleup-create'?'selected':'' }} value="scaleup-create">Buat Scaleup</option>
                                <option {{ $approval->transaction_type=='scaleup-edit'?'selected':'' }} value="scaleup-edit">Edit Scaleup</option>
                                <option {{ $approval->transaction_type=='scaleup-print'?'selected':'' }} value="scaleup-print">Cetak Scaleup</option>
                                <option {{ $approval->transaction_type=='scaleup-view'?'selected':'' }} value="scaleup-view">Lihat Scaleup</option>
                                <option {{ $approval->transaction_type=='formula-create'?'selected':'' }} value="formula-create">Buat Formula</option>
                                <option {{ $approval->transaction_type=='formula-edit'?'selected':'' }} value="formula-edit">Edit Formula</option>
                                <option {{ $approval->transaction_type=='formula-print'?'selected':'' }} value="formula-print">Cetak Formula</option>
                                <option {{ $approval->transaction_type=='formula-view'?'selected':'' }} value="formula-view">Lihat Formula</option>             
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="doctype" class="form-label">Departement</label>
                            <select name="doctype" id="doctype" class="form-select @error('doctype') is-invalid @enderror"
                                required>
                                {{-- <option value="{{ $approval->doctype_id }}">{{ $approval->doctype . ' - ' . $approval->description  }}</option> --}}
                                @foreach ($doctype as $item)
                                    <option {{ old('doctype') == $item->id ? 'selected' : ($approval->doctype_id==$item->id? 'selected' : '') }} value="{{ $item->id }}">
                                        {{ $item->doctype . ' - ' . $item->description }}</option>
                                @endforeach
                            </select>
                            @error('doctype')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="user" class="form-label">Pilih User</label>
                            <select name="user" id="user" class="form-select @error('user') is-invalid @enderror"
                                required>
                                
                                @foreach ($users as $item)
                                    <option {{ old('user') == $item->id ? 'selected' : ($approval->user_id== $item->id?'selected': '') }} value="{{ $item->id }}">
                                        {{ $item->name . ' - ' . $item->email }}</option>
                                @endforeach
                            </select>
                            @error('user')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="level" class="form-label">level</label>
                            <input type="number" class="form-control bg-white @error('level') is-invalid @enderror"
                                id="level" name="level" value="{{ old('level') ? old('level') : $approval->level }}"
                                placeholder="Level " min="1" max="9" required />

                            @error('level')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" name="is_active" {{ $approval->is_active? 'checked':'' }} type="checkbox" id="is_active">
                                <label class="form-check-label" for="is_active" id="switchLabel">{{ $approval->is_active?'Aktif':'Nonaktif' }}</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                            <button name="action" class="btn btn-outline-warning" value="D" type="submit">{{$approval->is_deleted?"Undelete" : 'Delete'  }}</button>
                            <button name="action" class="btn btn-primary" value="U" type="submit">Update Approval</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
        </div>
    </div>

    @push('custom-script')
    <script>
        $(document).ready(function(){
            $('#is_active').change(function() {
                if ($(this).is(':checked')) {
                    $('#switchLabel').text('Aktif');
                } else {
                    $('#switchLabel').text('Nonaktif');
                }
            });
        });
    </script>
    @endpush
@endsection
