@extends('layouts.template')

@section('title', 'Pilih transaksi')


@section('content')
    <div class="row">
        <div class="col">

            @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
            <div class="card">


                <div class="card-body">
                    @if ($cekmenu)
                        <h5>Anda masih mempunyai request pending, masukan otp untuk melanjutkan</h5>
                        <p> {{ $cekmenu->otp }} </p>

                        <form action="{{ route('verifikasiOtp') }}" method="post">
                            @csrf
                            <div class="col-md-6 mb-3">
                                <label for="permission_name" class="form-label">Transaksi</label>
                                <input autocomplete="off" type="text"
                                    class="form-control  @error('permission_name') is-invalid	@enderror"
                                    id="permission_name" name="permission_name"
                                    value="{{ old('permission_name') ? old('permission_name') : $cekmenu->permission_name }}"
                                    placeholder="Deskripsi" readonly />
                                @error('permission_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="otp" class="form-label">Input OTP</label>
                                <input autocomplete="off" type="text"
                                    class="form-control  @error('otp') is-invalid	@enderror" id="otp" name="otp"
                                    value="" placeholder="OTP" required />
                                @error('otp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>


                            <div class="d-flex justify-content-start gap-3">
                                {{-- <a href="{{ route('sub-div.index') }}" class="btn btn-outline-secondary">Batal</a> --}}
                                <button class="btn btn-primary" type="submit">Verifikasi</button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('requestMenu') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="scaleup-option">Choose an option:</label>
                                <div>
                                    <input type="radio" id="scaleup-create" name="menu" value="scaleup-create">
                                    <label for="scaleup-create">Scaleup Create</label>
                                </div>
                                <div>
                                    <input type="radio" id="scaleup-view" name="menu" value="scaleup-view">
                                    <label for="scaleup-view">Scaleup View</label>
                                </div>
                                <div>
                                    <input type="radio" id="scaleup-print" name="menu" value="scaleup-print">
                                    <label for="scaleup-view">Scaleup Print</label>
                                </div>
                                <div>
                                    <input type="radio" id="scaleup-compare" name="menu" value="scaleup-compare">
                                    <label for="scaleup-view">Scaleup Compare</label>
                                </div>
                            </div>


                            <div class="mt-3">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary" type="submit">Request</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>


@endsection
