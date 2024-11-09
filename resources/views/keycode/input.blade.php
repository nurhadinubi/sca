@extends('layouts.template')

@section('title', $title ?? 'Proses Keycode')
@section('content')
<div class="col-12">
  @if (session('message'))
      @include('components.flash', [
          'type' => session('message')['type'],
          'text' => session('message')['text'],
      ])
  @endif
</div>
    <form action="{{ route('keycode.process') }}" method="post" autocomplete="off">
        <div class="row">
          @csrf
            <div class="col-md-6 mb-3 row">
                <label for="keycode" class="col-form-label col-sm-2">Keycode</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="keycode" name="keycode" value="" required autocomplete="off">
                </div>
            </div>

            <div class="col-md-2 mb-3 row">
                <button type="submit" class="btn btn-outline-primary inline-block">Proses</button>
            </div>
        </div>
    </form>

    <p class="mt-4">Belum punya keycode? <a href="{{ route('keycode.menu') }}"> Request Keycode</a></p>
    <hr>
@endsection
