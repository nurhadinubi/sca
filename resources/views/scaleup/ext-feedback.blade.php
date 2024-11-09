@extends('layouts.external')
@section('title', $title ?? 'Status Approve Dokumen')
@section('content')
    <div class="d-flex flex-column align-items-center">
            <h1>{{ $message??"Status Dokumen" }}</h1>  
    </div>
@endsection
