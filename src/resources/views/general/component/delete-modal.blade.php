<div class="modal fade" id="confirm-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-warning"></i> Alert</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Are you sure want to do this?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary modal-yes" data-dismiss="modal">Yes</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>
@push("script")
<script>
    $(".delete").click(function(){
        $(".modal-yes").data("href",$(this).data("href"));
    })

    $(".modal-yes").click(function(){
        window.location = $(this).data("href");
    })
</script>
@endpush