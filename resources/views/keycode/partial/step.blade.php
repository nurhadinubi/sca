<ul class="list-group">
  <li class="list-item row">
    <span class="col-1">Level</span>
    <span class="col-5">Email</span>
    <span class="col-2">Tgl. kirim</span>
    <span class="col-2">Tgl. proses</span>
    <span class="col-2">Status</span>
  </li>
 
  @foreach ($approver as $step)
      <li class="list-item row">
        <span class="col-1">{{ $step->level }}</span>
        <span class="col-5">{{ $step->email }}</span>
        <span class="col-2">{{ $step->sent_at }}</span>
        <span class="col-2">{{ $step->updated_at }}</span>
        
        <span class="col-2">
          @if ($step->status=='P')
              Pending
          @elseif($step->status=='A')
              Approve
          @else
            Reject
          @endif
        </span>
      </li>
  @endforeach
</ul>
