@extends('layouts.template')

@section('title', 'Tambah Divisi')

@section('content')
    <div class="row">
        <div class="col">

            <div class="card">
                <div class="card-body">

                    <form class="row g-3 needs-validation" autocomplete="off" method="POST" action="{{ route('sub-div.update',['id'=>$sub->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label for="division" class="form-label">Divisi</label>
                            <select name="division" id="division" class="form-select @error('division') is-invalid	@enderror">
                                @if (count($divisions)>0)
                                    @foreach ($divisions as $item)
                                        <option {{ $item->id == $sub->doctype_id? "selected":'' }} value="{{ $item->id }}"> {{ $item->doctype ." - ". $item->description }} </option>
                                    @endforeach               
                                @else
                                <option value=""> Tambahkan divisi dahulu</option>
                                @endif

                            </select>
                            @error('division')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="code" class="form-label">Kode</label>
                            <input autocomplete="off" type="text" class="form-control  @error('code') is-invalid	@enderror" id="code"
                                name="code" value="{{ old('code') ? old('code') : $sub->code }}" placeholder="Kode" />
                            @error('code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-7">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input autocomplete="off" type="text" class="form-control  @error('description') is-invalid	@enderror" id="description"
                                name="description" value="{{ old('description') ? old('description') : $sub->description }}" placeholder="Deskripsi" />
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('sub-div.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
        </div>
    </div>
@endsection
