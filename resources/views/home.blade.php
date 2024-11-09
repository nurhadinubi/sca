@extends('layouts.template')

@section('title', 'HOME')

@section('content')
    <div class="row">
        @if (session('message'))
                @include('components.flash', [
                    'type' => session('message')['type'],
                    'text' => session('message')['text'],
                ])
            @endif
       HOME
    </div>
@endsection
