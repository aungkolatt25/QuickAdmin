<li class="item">
	<div class="item-row">

			<div class="item-col fixed item-col-check">
			</div>
			
			<div class="item-col fixed pull-left item-col-title">
			</div>
			<?php
				$c=0;
				if(!isset($rc))
					$rc=1;

				$rc++;
				foreach ($quickdata->getVisibleColumns("list") as $key => $column) {
					$c++;
			?>
			<div class="item-col fixed pull-left item-col-owner">
				<div class="item-heading">{{qt($column->displayName)}}</div>
				<div>
						<h4 class="item-title">
							{{$totalData->{$column->getName()}}}
						</h4>
				</div>
			</div>
			<?php
				}
			?>
			
			<div class="item-col fixed item-col-actions-dropdown">
				
			</div>

		</div>
</li>