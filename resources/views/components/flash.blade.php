
@if ($type == 'success')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>{{ $text }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($type == 'error')
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>{{ $text }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($type == 'warning')
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>{{ $text }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif