<li class="item">
	<div class="item-row">
		<div class="item-col fixed item-col-check">
			<label class="item-check" id="select-all-items">
				<input type="checkbox" class="checkbox">
				<span></span>
			</label>
		</div>
		
		<div class="item-col fixed pull-left item-col-title">
		</div>
		@foreach($quickdata->getVisibleColumns("list") as $column)
			<div class="item-col fixed pull-left item-col-owner display-{{$column->type}}">
				<div class="item-heading">{{qt($column->displayName)}}</div>
				<div>
					<h4 class="item-title">
						{{ $totalData->{"total_".$column->name} }}
					</h4>
				</div>
			</div>
			<td></td>
		@endforeach
	</div>
</li>
