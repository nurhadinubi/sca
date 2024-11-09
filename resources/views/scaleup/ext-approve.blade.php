@extends('layouts.external')
@section('title', $title ?? 'Approve Dokumen')
@section('content')

    <div class="d-flex flex-column align-items-center">
            <h1>Approve Dokumen Scaleup</h1>
            @if ($approval_doc->status=='P')
            <form action="" method="post">
              @csrf
                <button class="btn btn btn-outline-warning" name="action" value="R" type="submit">Reject</button>
                <button class="btn btn btn-outline-primary" name="action" value="A" type="submit">Approve</button>
            </form>
            @elseif($approval_doc->status=='R')
                <h2>Dokumen sudah Direject</h2>
            @else
            <h2>Dokumen sudah Diproses</h2>
            @endif
            
    </div>
@endsection
