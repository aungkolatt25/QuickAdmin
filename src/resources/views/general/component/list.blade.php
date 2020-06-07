<div class="card items">
	<ul class="item-list striped">
		<li class="item item-list-header">
			<div class="item-row">
				<div class="item-col fixed item-col-check">
					<label class="item-check" id="select-all-items">
						<input type="checkbox" class="checkbox">
						<span></span>
					</label>
				</div>
				
                @foreach($quickdata->getVisibleColumns("list") as $column)
                <div class="item-col item-col-header fixed item-col-owner md">
                    <div>
                        <span>{{qt($column->displayName)}}</span>
                    </div>
                </div>
                @endforeach

				<div class="item-col item-col-header fixed item-col-actions-dropdown">

				</div>
			</div>
		</li>
        @foreach($datas as $data)
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
                                {{ $column->getUI("list", compact("data")) }}
                            </h4>
                        </div>
                    </div>
                    <td></td>
                @endforeach
                
                <div class="item-col fixed item-col-actions-dropdown">
                    <div class="item-actions-dropdown">
                        <a class="item-actions-toggle-btn">
                            <span class="inactive">
                                <i class="fa fa-cog"></i>
                            </span>
                            <span class="active">
                            <i class="fa fa-chevron-circle-right"></i>
                            </span>
                        </a>
                        <div class="item-actions-block">
                            <ul class="item-actions-list">
                                <li>
                                    <a class="remove delete" href="#" data-toggle="modal" data-target="#confirm-modal" data-href="{{qurl($quickdata->file.'/delete/'.$data->id)}}";>
                                        <i class="fa fa-trash-o "></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="edit" href="{{qurl($quickdata->file.'/edit/'.$data->getKey())}}">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                                <li>
                                    <a class="edit" href="{{qurl($quickdata->file.'/edit/'.$data->getKey())}}">
                                        <i class="fa fa-list"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </li>
        @endforeach
		<?php //endForEach;
		//include __DIR__."/component/total.php";?>
	</ul>
</div>
<!-- <button class="print btn btn-secondary" data-print="<?php //eurl(__CONTROLLER."/printData/$url?{$search}");?>">Print</button> -->
<a href="<?php //eurl(__CONTROLLER."/printing/$url");echo "?{$search}";?>" target="_blank" class="btn btn-primary">Print</a>
<nav class="text-right">
    @if($quickdata->isPaginate())
        {{$datas->links()}}
    @endif
</nav>