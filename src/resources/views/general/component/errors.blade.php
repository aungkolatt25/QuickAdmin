@if($errors->any())
<div class="modal" tabindex="-1" role="dialog" id="errorModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Error</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @if($errors->any())
            {!! implode('', $errors->all('<div>:message</div>')) !!}
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@push("script")
<script>
    $('#errorModal').modal("show");
</script>
@endpush
@endif